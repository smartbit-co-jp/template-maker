<?php 

Namespace App\CustomClasses;

class MoneyTiming
{
	public $type;
	public $value;
	public function __construct(int $value = null, $type = 0)
	{
		$this->type = $type;
		$this->value = $value;

	}
	public function __toString()
	{
		if ( ($this->type || $this->type == 0) && $this->value ) {
			return $this->describeMoney($this);
		}
		return '';
	}
	public function withLocale($locale)
	{
		if ( ($this->type || $this->type == 0) && $this->value ) {
			return $this->describeMoney($this, $locale);
		}
		return '';
	}

	public function describeMoney(object $money_object = null, string $locale = null)
	{
		if ($money_object) {
			if (is_numeric($money_object->value)) {
				return trans_choice(
					'global.describe_money',
					$money_object->type,
					[
						'value' => '¥' . number_format($money_object->value)
					],
					$locale
				);
			}
		}
	}
}