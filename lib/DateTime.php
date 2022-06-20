<?php
/**
 * @package ActiveRecord
 */

namespace ActiveRecord;

/**
 * An extension of PHP's DateTime class to provide dirty flagging and easier formatting options.
 *
 * All date and datetime fields from your database will be created as instances of this class.
 *
 * Example of formatting and changing the default format:
 *
 * <code>
 * $now = new ActiveRecord\DateTime('2010-01-02 03:04:05');
 * ActiveRecord\DateTime::$DEFAULT_FORMAT = 'short';
 *
 * echo $now->format();         # 02 Jan 03:04
 * echo $now->format('atom');   # 2010-01-02T03:04:05-05:00
 * echo $now->format('Y-m-d');  # 2010-01-02
 *
 * # __toString() uses the default formatter
 * echo (string)$now;           # 02 Jan 03:04
 * </code>
 *
 * You can also add your own pre-defined friendly formatters:
 *
 * <code>
 * ActiveRecord\DateTime::$FORMATS['awesome_format'] = 'H:i:s m/d/Y';
 * echo $now->format('awesome_format')  # 03:04:05 01/02/2010
 * </code>
 *
 * @package ActiveRecord
 * @see http://php.net/manual/en/class.datetime.php
 */
class DateTime extends \DateTime implements DateTimeInterface, \JsonSerializable
{
	/**
	 * Default format used for format() and __toString()
	 */
	public static $DEFAULT_FORMAT = 'rfc2822';

	/**
	 * Pre-defined format strings.
	 */
	public static $FORMATS = array(
		'db' => 'Y-m-d H:i:s',
		'number' => 'YmdHis',
		'time' => 'H:i',
		'short' => 'd M H:i',
		'long' => 'F d, Y H:i',
		'atom' => \DateTime::ATOM,
		'cookie' => \DateTime::COOKIE,
		'iso8601' => \DateTime::ISO8601,
		'rfc822' => \DateTime::RFC822,
		'rfc850' => \DateTime::RFC850,
		'rfc1036' => \DateTime::RFC1036,
		'rfc1123' => \DateTime::RFC1123,
		'rfc2822' => \DateTime::RFC2822,
		'rfc3339' => \DateTime::RFC3339,
		'rss' => \DateTime::RSS,
		'w3c' => \DateTime::W3C);

	private $model;
	private $attribute_name;

	// -----------------------------------------------------------------------------------------------------------------
	// Implementation overrides

	public function add($interval): \DateTime
	{
		$this->flag_dirty();
		return parent::add($interval);
	}

	public static function createFromFormat($format, $time, $tz = null): \DateTime|false
	{
		$phpDate = $tz ? parent::createFromFormat($format, $time, $tz) : parent::createFromFormat($format, $time);
		if (!$phpDate)
			return false;
		// convert to this class using the timestamp
		$ourDate = new static('', $phpDate->getTimezone());
		$ourDate->setTimestamp($phpDate->getTimestamp());
		return $ourDate;
	}

	// createFromImmutable

	// createFromInterface

	// getLastErrors

	public function modify($modify): \DateTime
	{
		$this->flag_dirty();
		return parent::modify($modify);
	}

	// __set_state

	public function setDate($year, $month, $day): \DateTime
	{
		$this->flag_dirty();
		return parent::setDate($year, $month, $day);
	}

	public function setISODate($year, $week, $day = 1): \DateTime
	{
		$this->flag_dirty();
		return parent::setISODate($year, $week, $day);
	}

	public function setTime($hour, $minute, $second = NULL, $microseconds = NULL): \DateTime
	{
		$this->flag_dirty();
		return parent::setTime($hour, $minute, $second, $microseconds);
	}

	public function setTimestamp($unixtimestamp): \DateTime
	{
		$this->flag_dirty();
		return parent::setTimestamp($unixtimestamp);
	}

	public function setTimezone($timezone): \DateTime
	{
		$this->flag_dirty();
		return parent::setTimezone($timezone);
	}

	public function sub($interval): \DateTime
	{
		$this->flag_dirty();
		return parent::sub($interval);
	}

	// diff

	public function format($format = null): string
	{
		return parent::format(self::get_format($format));
	}

	// getOffset

	// getTimestamp

	// getTimezone

	// __wakeUp

	// -----------------------------------------------------------------------------------------------------------------
	// Activerecord extensions

	public static function get_format($format = null)
	{
		// use default format if no format specified
		if (!$format)
			$format = self::$DEFAULT_FORMAT;

		// format is a friendly
		if (array_key_exists($format, self::$FORMATS))
			return self::$FORMATS[$format];

		// raw format
		return $format;
	}

	public function attribute_of($model, $attribute_name)
	{
		$this->model = $model;
		$this->attribute_name = $attribute_name;
	}

	private function flag_dirty()
	{
		if ($this->model)
			$this->model->flag_dirty($this->attribute_name);
	}

	public function __toString()
	{
		return $this->format();
	}

	public function __clone()
	{
		$this->model = null;
		$this->attribute_name = null;
	}

	// -----------------------------------------------------------------------------------------------------------------
	// JsonSerializable

	public function jsonSerialize(): mixed
	{
		return $this->format("c");
	}
}
