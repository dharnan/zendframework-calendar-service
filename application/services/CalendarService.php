<?php

/**
 * Service_Calendar (using Zend_Date)
 *
 * @category
 * @package    Service_Calendar
 *
 */
class Service_Calendar
{

	protected $_now; //todays date
	protected $_focusDate; //date in focus
	protected $_focusMonthNames;
	protected $_focusMonthDayNames;
	protected $_focusMonthNumDays;
	protected $_focusMonthFirstDayOfWeek;
	protected $_focusMonthNumWeeks;
	protected $_validDates; //months in range of now
	protected $_nextMonth;
	protected $_prevMonth;

	/**
	 * @param String $date
	 */
	public function __construct($date = null)
	{
		$this->_now = Zend_Date::now(); //today

		$this->setFocusDate($date);
	}

	/**
	 * Sets the month in focus, i.e. the month being displayed in the calendar
	 *
	 * @param String $date
	 */
	public function setFocusDate($date = null)
	{
		//
		$this->_focusDate = new Zend_Date($date, "M yy");
			
		//date params
		$this->_initServiceDateParams($this->_focusDate);
	}

	/**
	 * Sets up the Calendar Service's params
	 * @param Zend_Date $date
	 */
	protected function _initServiceDateParams(Zend_Date $date)
	{
		$this->_focusMonthNames = Zend_Locale::getTranslationList('Month'); //locale month list
		$this->_focusMonthDayNames = Zend_Locale::getTranslationList('Day'); //locale day list
		$this->setValidDates();
		$this->_focusMonthNumDays = $date->get(Zend_Date::MONTH_DAYS);
		$this->_setNextMonth($date);
		$this->_setPrevMonth($date);
		$this->_focusMonthFirstDayOfWeek = $date->get(Zend_Date::WEEKDAY_DIGIT);
		$this->_focusMonthNumWeeks = ceil(($this->getFocusMonthFirstDayOfWeek() + $this->getFocusMonthNumDays()) / 7);
	}

	/**
	 * Sets an aarray of months in range of the month we're physically in (i.e. NOW)
	 * @param int $startOffset
	 * @param int $endOffset
	 */
	public function setValidDates($startOffset = -11, $endOffset = 11)
	{
		$tmp = clone $this->_now;
		$startMonth = $tmp->subMonth(abs($startOffset));

		$this->_validDates = array();
		array_push($this->_validDates, $startMonth);

		$tmp = clone $startMonth;
		for ($i = 0; $i < (abs($startOffset) + abs($endOffset)); $i++) {
			$nextMonth = $tmp->addMonth(1);
			array_push($this->_validDates, $nextMonth);
			$tmp = clone $nextMonth;
		}
		unset($tmp);
	}

	/**
	 * Sets the next month (after the focus date)
	 * @param Zend_Date $date
	 */
	protected function _setNextMonth(Zend_Date $date)
	{
		$focusDateClone = clone $date;
		$this->_nextMonth = $focusDateClone->addMonth(1);
	}

	/**
	 * Sets the prev month (before the focus date)
	 * @param Zend_Date $date
	 */
	protected function _setPrevMonth(Zend_Date $date)
	{
		$focusDateClone = clone $date;
		$this->_prevMonth = $focusDateClone->subMonth(1);
	}

	/**
	 * @param String $controller
	 * @param String $action
	 * @return Array $calHeader
	 */
	public function getCalendarHeaderDataArray($controller=null,$action=null) 
	{		
		$calHeader = array();
		$focusDate = $this->getFocusDate()->get("MMM yyyy");		
		foreach ($this->getValidDates() as $date) {
			$arr = array();			
			$arr['id'] = ($date->get('MMM yyyy') == $focusDate) ? 'selected-month' : $date->get('MMMyyyy');
			if (null == $controller && null !== $action) {
				$arr['url'] = array(
					'controller' => $controller,
					'action' => $action,
			        'month' => $date->get('M'),
			        'year' => $date->get('yyyy')
				);
			} else {
				$arr['url'] = array(					
			        'month' => $date->get('M'),
			        'year' => $date->get('yyyy')
				);
			}
			
			$arr['text'] = $date->get('MMM yyyy');
			array_push($calHeader, $arr);			
		}
		return $calHeader;
	}
	
	/**
	 * @return Array $calWeekdays
	 */
	public function getCalendarWeekdayDataArray() 
	{
		$c = 1;
		$calWeekdays = array();
		foreach ($this->getFocusMonthDayNames() as $dayShort => $day_long) {
			$class = '';
			if ($c == 1) {
				$class .= 'first';
			} elseif ($c == 7) {
				$class .= 'last';
			}
			$c++;
			array_push($calWeekdays, array(
		        'class' => $class,
		        'dayShortStr' => strtoupper($dayShort)
			));
		}
		return $calWeekdays;
	}
	
	/**
	 * @return Array $calMonthDays
	 */
	public function getCalendarMonthDayDataArray()
	{
		$today = $this->getNow()->get("d");
		$nowDate = $this->getNow()->get("MMM yyyy");
		$focusDate = $this->getFocusDate()->get("MMM yyyy");
		
		$calDayNum = 1; //first day
		$calMonthDays = array();
		for ($i = 0; $i < $this->getFocusMonthNumWeeks(); $i++) {
			$weekArr = array();
			for ($j = 0; $j < 7; $j++) {
				$dayArr = array();
				$cellNum = ($i * 7 + $j);
				$dayArr['class'] = "";
				//css class cals
				if (($nowDate == $focusDate) && ($today == $calDayNum) && ($cellNum >= $this->getFocusMonthFirstDayOfWeek())) { //today
					$dayArr['class'] = " today";
				}
				if ($j == 0) { //first day of week
					$class .= ' first';
				} elseif ($j == 6) { //last day of week
					$dayArr['class'] .= ' last';
				}
				if ($i == ($this->getFocusMonthNumWeeks() - 1)) { //last week of days
					$dayArr['class'] .= ' bottom';
				}
				//build the days of the month cell data
				$firstDayOfWeek = $this->getFocusMonthFirstDayOfWeek();
				if ($cellNum >= $firstDayOfWeek && $cellNum < ($this->getFocusMonthNumDays() + $firstDayOfWeek)) { //day in cell
					$dayArr['num'] = Zend_Locale_Format::toNumber($calDayNum);
					$calDayNum++;
				}
				array_push($weekArr, $dayArr);
			}
			array_push($calMonthDays, $weekArr);
		}
		return $calMonthDays;
	}

	/**
	 * @return Zend_Date
	 */
	public function getNow()
	{
		return $this->_now;
	}

	/**
	 * @return Zend_Date
	 */
	public function getFocusDate()
	{
		return $this->_focusDate;
	}

	/**
	 * @return Array
	 */
	public function getFocusMonthNames()
	{
		return $this->_focusMonthNames;
	}

	/**
	 * @return Array
	 */
	public function getFocusMonthDayNames()
	{
		return $this->_focusMonthDayNames;
	}

	/**
	 * @return Array
	 */
	public function getValidDates()
	{
		return $this->_validDates;
	}

	/**
	 * @return int
	 */
	public function getFocusMonthNumDays()
	{
		return $this->_focusMonthNumDays;
	}

	/**
	 * @return int
	 */
	public function getFocusMonthFirstDayOfWeek()
	{
		return $this->_focusMonthFirstDayOfWeek;
	}

	/**
	 * @return int
	 */
	public function getFocusMonthNumWeeks()
	{
		return $this->_focusMonthNumWeeks;
	}

	/**
	 * @return Zend_Date
	 */
	public function getNextMonth()
	{
		return $this->_nextMonth;
	}

	/**
	 * @return Zend_Date
	 */
	public function getPrevMonth()
	{
		return $this->_prevMonth;
	}
}
