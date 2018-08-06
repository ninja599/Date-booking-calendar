<?php
// Calendar builder
Class Calendar {
	
	private $month, $year, $week_starts_on, $num_days, $date_info, $day_of_week, $booked_days, $today;
	
	public function __construct($month, $year, $week_starts_on = 'm', $daynames = array('M','T','W','T','F','S','S')) 
	{
		date_default_timezone_set('Europe/Helsinki');
		$this->today = date('Y-m-d', time());
		$this->month = $month;	
		$this->year = $year;
		$this->week_starts_on = $week_starts_on;
		$this->num_days = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
		$this->date_info = getdate(strtotime('first day of', mktime(0,0,0,$this->month,1,$this->year)));
		$this->day_of_week = $this->date_info['wday'];
		
		switch ($week_starts_on) {
			default:
				// Falltrough
			case 'm':
				$this->days_of_week = array($daynames[0],$daynames[1],$daynames[2],$daynames[3],$daynames[4],$daynames[5],$daynames[6]);
				if ($this->day_of_week == 0) {
					$this->day_of_week = 6;
				} else {
					$this->day_of_week--;
				}
				break;
			case 's':
				$this->days_of_week = array($daynames[6],$daynames[0],$daynames[1],$daynames[2],$daynames[3],$daynames[4],$daynames[5]);
				break;
		}
	}
	
	public function show()
	{	
		date_default_timezone_set('Europe/Helsinki');
		$today = date('Y-m-d', time());
		
		// Month caption
		$output = "\n".'<div class="calmonth" id="'.sprintf("%02d", $this->month).'-'.$this->year.'">'."\n";
		$output .= '<table>';
		$output .= '
			<div id="monthcaption">
				<a href="?ym='.$this->year.'-'.sprintf("%02d", $this->month).'">' . 
				$this->date_info['month'] . "
				</a>
			</div>
			";
		$output .= "<tr>\n";
		
		// Header row of Weekday names
		foreach ($this->days_of_week as $day) {
			$output .= '<th class="monthheader">' . $day . "</th>\n";
		}
		
		// Close header row and open first row for days
		$output .= "</tr><tr>\n";
		
		// Where to place first day of month
		if ($this->day_of_week > 0) {
			$output .='<td class="nb" colspan="'. $this->day_of_week .'"></td>'."\n";
		}

		// Start num_days counter
		$current_day = 1;
		
		// Loop and build days
		while ($current_day <= $this->num_days) {
			$current_date = $this->year.'-'.sprintf("%02d", $this->month).'-'.sprintf("%02d", $current_day);
			// Reset day_of_week counter and close each row if end of row
			if ($this->day_of_week == 7) {
				$this->day_of_week = 0;
				$output .= "</tr><tr>\n";
			}

			
			// Build each day cell
			if ($today == $current_date) {
				$output .= '<td buclass="today" date="'.$current_date.'" name="date" class="today" id="'.$current_date.'">' . $current_day . "</td>\n";
			} elseif ($this->day_of_week == 5 || $this->day_of_week == 6) {
				$output .= '<td buclass="weekend" date="'.$current_date.'" name="date" class="weekend" id="'.$current_date.'">' . $current_day . "</td>\n";
			} else {
				$output .= '<td buclass="weekday" date="'.$current_date.'" name="date" class="weekday" id="'.$current_date.'">' . $current_day . "</td>\n";
			}
			
			// Increment counters
			$current_day++;
			$this->day_of_week++;
		}
		
		// Once num_days counter ends we need filler cells if not last day of week
		if ($this->day_of_week != 7) {
			$remaining_days = 7 - $this->day_of_week;
			$output .='<td class ="nb" colspan="'. $remaining_days .'"></td>'."\n";
		}
		
		// Close final row and table
		$output .= "</tr>\n";
		$output .= "</table>\n";
		$output .= "</div>\n";

		echo $output;
	}
}
?>