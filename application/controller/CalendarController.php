<?php
class CalendarController
{
	/**
	 *
	 */
	public function init()
	{
	}

	/**
	 * 
	 */
	public function viewAction()
	{
		//Request params
		$request = $this->getRequest();
		$month = $request->getParam('month', date('F'));
		$year = $request->getParam('year', date('Y'));

		//CalendarService
		$calendarService = new Service_Calendar("$month $year");
		$calendarService->setValidDates(-11, 4); //11 months back, 4 months forward

		//Calendar vars
		$today = $calendarService->getNow()->get("d");
		$nowDate = $calendarService->getNow()->get("MMM yyyy");
		$focusDate = $calendarService->getFocusDate()->get("MMM yyyy");

		/**
		 * Build the calendar header data
		 */
		$this->view->monthHeader = $calendarService->getFocusDate()->get("MMMM yyyy");
		$this->view->calHeader = array();
		foreach ($calendarService->getValidDates() as $date) {
			$arr = array();
			$arr['id'] = ($date->get('MMM yyyy') == $focusDate) ? 'selected-month' : $date->get('MMMyyyy');
			$arr['url'] = array(
				'controller' => 'calendar',
				'action' => 'view',
		        'month' => $date->get('M'),
		        'year' => $date->get('yyyy')
			);
			$arr['text'] = $date->get('MMM yyyy');
			array_push($this->view->calHeader, $arr);
		}

		/**
		 * Build the calendar weekdays data
		 */
		$this->view->calWeekdays = array();
		$c = 1;
		foreach ($calendarService->getFocusMonthDayNames() as $dayShort => $day_long) {
			$class = '';
			if ($c == 1) {
				$class .= 'first';
			} elseif ($c == 7) {
				$class .= 'last';
			}
			$c++;
			array_push($this->view->calWeekdays, array(
		        'class' => $class,
		        'dayShortStr' => strtoupper($dayShort)
			));
		}

		/**
		 * Build the calendar monthdays data
		 */
		$calDayNum = 1; //first day
		$this->view->calMonthDays = array(); //rows
		for ($i = 0; $i < $calendarService->getFocusMonthNumWeeks(); $i++) {
			$weekArr = array();
			for ($j = 0; $j < 7; $j++) {
				$dayArr = array();
				$cellNum = ($i * 7 + $j);
				$dayArr['class'] = "";
				//css class cals
				if (($nowDate == $focusDate) && ($today == $calDayNum) && ($cellNum >= $calendarService->getFocusMonthFirstDayOfWeek())) { //today
					$dayArr['class'] = " today";
				}
				if ($j == 0) { //first day of week
					$class .= ' first';
				} elseif ($j == 6) { //last day of week
					$dayArr['class'] .= ' last';
				}
				if ($i == ($calendarService->getFocusMonthNumWeeks() - 1)) { //last week of days
					$dayArr['class'] .= ' bottom';
				}
				//build the days of the month cell data
				$firstDayOfWeek = $calendarService->getFocusMonthFirstDayOfWeek();
				if ($cellNum >= $firstDayOfWeek && $cellNum < ($calendarService->getFocusMonthNumDays() + $firstDayOfWeek)) { //day in cell
					$dayArr['num'] = Zend_Locale_Format::toNumber($calDayNum);
					$calDayNum++;
				}
				array_push($weekArr, $dayArr);
			}
			array_push($this->view->calMonthDays, $weekArr);
		}

		//render the calendar view script
		$this->view->render('view-calendar.php');
	}
}