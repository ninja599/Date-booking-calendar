(function(){
  
  const dates = document.querySelectorAll('[name=date]');
  const monthsDiv = document.getElementById('calendar');
  const clearlink = document.getElementById('clearlink');
  const booklink = document.getElementById('booklink');
  const backlink = document.getElementById('now');
  const selectioninfo = document.getElementById('selection');
  let arrive = null;
  let arrivedate = null;
  let leave = null;
  let leavedate = null;

  function compare3(a,b,c) {
    if((a - c) * (a - c) < (b - c) * (b - c)) {
      return a;
    } else {
      return b;
    }
  }

  function padsingledigits(digit){
    if (digit < 10 && digit != 0) {
      let newdigit = '0'+digit;
      return newdigit;
    } else {
      return digit;
    }
  }

  function formatDate(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();

      if (month.length < 2) month = '0' + month;
      if (day.length < 2) day = '0' + day;

      return [year, month, day].join('-');
  }

  function selectArrive(date){
    arrive = date;
    document.getElementById('arrivefield').value = arrive;
    clearlink.style.display = "inline";
    localStorage.setItem("lsarrive", arrive);
    document.querySelectorAll('[date="'+date+'"]')[0].className = "arrive";
  }

  function selectLeave(date) {
    leave = date;
    document.getElementById('leavefield').value = leave;
    booklink.style.display = "inline";
    localStorage.setItem("lsleave", leave);
    document.querySelectorAll('[date="'+date+'"]')[0].className = "leave";
    markstay(arrive,leave);
  }

  function removeArrive(){
    document.getElementById('arrivefield').value = '';
    let removearrive = document.getElementsByClassName("arrive")[0];
    localStorage.removeItem("lsarrive");
    arrive = null;
    arrivedate = null;
    if (removearrive) {removearrive.className = removearrive.getAttribute("buclass");}
    removearrive = null;
    selectioninfo.childNodes.item(0).innerHTML = '';
  }

  function removeLeave(){
    document.getElementById('leavefield').value = '';
    let removeleave = document.getElementsByClassName("leave")[0];
    localStorage.removeItem("lsleave");
    leave = null;
    leavedate = null;
    if (removeleave) {removeleave.className = removeleave.getAttribute("buclass");}
    removeleave = null;
    selectioninfo.childNodes.item(1).innerHTML = '';
  }

  function removeSelection(arriveorleave) {
    if(arriveorleave == 'arrive') {
      removeArrive();
    } else {
      removeLeave();
      }
    let removestay = document.getElementsByClassName("stay");
    if (removestay) {
      for (let i=removestay.length -1; i>=0; i--) {
        removestay[i].className = removestay[i].getAttribute("buclass");
      }
      removestay = null;
    }
  }

  function removeAll() {
    removeSelection('arrive');
    removeSelection('leave');
    clearlink.style.display = "none";
    booklink.style.display = "none";
    selectioninfo.childNodes.item(0).innerHTML = '';
    selectioninfo.childNodes.item(1).innerHTML = '';
  }

  function arrorlev(date) {
    if (arrive == date) {
      return 'arrive';
    }
    else {
      return 'leave';
    }
  }

  function dateClick(i) {
    return function(event) {
      if (event.target.getAttribute("name") == "date") {
        let date = event.target.getAttribute("date");
        
        if (arrive == null) {
          selectArrive(date);
        } else if (arrive != null && leave == null) {
          selectLeave(date);
         } else {
          let editdate = new Date(event.target.getAttribute("date"));
          let arriveorleave = arrorlev(compare3(arrivedate,leavedate,editdate).toISOString().substring(0, 10));
          removeSelection(arriveorleave);
          if (arriveorleave == 'arrive') {
            selectArrive(date);
          } else {
            selectLeave(date);
          }
          markstay (arrive, leave);
         }
      }
    };
  }

  function markstay (arrive, leave) {
    arrivedate = new Date(arrive);
    leavedate = new Date(leave);
    
    // Check if arrive is before leave and flips them if needed.
    if(arrivedate > leavedate) {
      let newArriveDate = leave;
      let newLeaveDate = arrive;
      removeAll();
      selectArrive(newArriveDate);
      selectLeave(newLeaveDate);
      markstay(newArriveDate, newLeaveDate);
    }
    
    // Mark stay period
    let booked = [];
    for ( let i = 0; i < dates.length; i++ ) {
      let checkdate = new Date(dates[i].id);
      if (checkdate > arrivedate && checkdate < leavedate) {
        if (dates[i].getAttribute("buclass") != "booked") {
          dates[i].className = ("stay");
        } else {
          booked.push(dates[i].getAttribute("date"));
        }
      }
    }
    
    // If there are previously booked dates in booked array, 
    // selected arrive or leave dates are modified and stay calculated again.
    if (booked[0] != null) {
      let bookedarrive = new Date(booked[0]);
      let bookedleave = new Date(booked[booked.length-1]);
      let arrdiff = bookedarrive - arrivedate;
      let leavediff = leavedate - bookedleave;
      if(arrdiff > leavediff) {
        let newLeaveDate = formatDate(bookedarrive.setDate(bookedarrive.getDate() - 1));
        removeSelection('leave');
        selectLeave(newLeaveDate);
        markstay(arrive, newLeaveDate);
      } else {
        let newArriveDate = formatDate(bookedleave.setDate(bookedleave.getDate() + 1));
        removeSelection('arrive');
        selectArrive(newArriveDate);
        markstay(newArriveDate, leave);
      }
    }
  }

  if (localStorage.getItem("lsarrive") != null) {
    arrive = localStorage.getItem("lsarrive");
    document.getElementById('arrivefield').value = arrive;
    clearlink.style.display = "inline";
    let arriveonpage = document.getElementById(arrive);
    if (arriveonpage) {
      arriveonpage.className = "arrive";
    }
  }

  if (localStorage.getItem("lsleave") != null) {
    leave = localStorage.getItem("lsleave");
    document.getElementById('leavefield').value = leave;
    booklink.style.display = "inline";
    let leaveonpage = document.getElementById(leave);
    if (leaveonpage) {
      leaveonpage.className = "leave";
    }
    markstay(arrive,leave);
  }

  monthsDiv.addEventListener('mouseover', (event) => {
    if (event.target.tagName == 'TD' && event.target.className !== 'nb') {
      document.body.style.cursor = "pointer";
      if (arrive == null) {
        event.target.id="arrive";
      } else if (arrive != null && leave == null){
        event.target.id="leave";
      } else {
        let editdate = new Date(event.target.id);
        let arriveorleave = arrorlev(compare3(arrivedate,leavedate,editdate).toISOString().substring(0, 10));
        if (arriveorleave == 'arrive') {
          event.target.id="arrive";
        } else {
          event.target.id="leave";
        }
      }
    }
  });

  monthsDiv.addEventListener('mouseout', (event) => {
    if (event.target.tagName == 'TD' && event.target.id !== 'nd') {
      document.body.style.cursor = "";
        event.target.id=event.target.getAttribute("date");
    }
  });

  for(let i=0; i< dates.length; i++) {
    dates[i].addEventListener('click', dateClick(i));
  }

  clearlink.addEventListener('click', (event) => {
    removeAll();
    clearlink.style.display = "none";
    booklink.style.display = "none";
  });

  booklink.addEventListener('click', (event) => {
    event.preventDefault();
    alert('book: '+formatDate(arrivedate)+' - '+formatDate(leavedate));
  });

  if (document.getElementById('yearnavhead')) {
    let yyyy = (new Date()).getFullYear();
    backlink.textContent = yyyy;
    if (yyyy != document.getElementById('yearnavhead').className) {
      backlink.style.display = 'inline';
      backlink.parentNode.href = '?y=' + (new Date()).getFullYear();
    }
  } else if (document.getElementById('monthnavhead')) {
    let datenow = new Date();
    let mm = datenow.getMonth()+1;
    let yyyy = datenow.getFullYear();
    let monthnow = yyyy+'-'+padsingledigits(mm);
    backlink.textContent = mm + ' / ' + yyyy;
    if (monthnow != document.getElementById('monthnavhead').className) {
      backlink.style.display = 'inline';
      backlink.parentNode.href = '?ym=' + monthnow;
    }
  }
  
})();