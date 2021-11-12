<?php 

Namespace App\CustomClasses;

class RelativeDay
{
	public $modifier;
	public $day;
	public function __construct(int $day, $modifier = 0)
	{
		$this->modifier = $modifier;
		$this->day = $day;

	}
	public function __toString()
	{
		if ( ($this->modifier || $this->modifier == 0) && $this->day ) {
			return $this->describeDay($this);
		}
		return '';
	}

	public function describeDay(object $day_object = null, string $locale = null)
	{
		if (($day_object->day ?? null)) {
			return trans_choice('global.describe_day', $day_object->modifier, ['day' => trans_choice('global.formated_day', $day_object->day, ['day' => $day_object->day], $locale)], $locale);
		}
	}
}