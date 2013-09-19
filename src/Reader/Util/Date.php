<?php

namespace Reader\Util;

class Date
{

	/**
	 * @param \DateTime|string $date
	 * @return string
	 * @author Selfoss - Tobias Zeising <tobias.zeising@aditu.de>
	 */
	public static function toHumanReadable($date)
	{
		if (is_string($date)) {
			$date = new \DateTime($date);
		}

		$now = new \DateTime();

		$secondsDiff = $now->getTimestamp() - $date->getTimestamp();
		$minutesDiff = $secondsDiff / 60;
		$hoursDiff = $minutesDiff / 60;
		$daysDiff = $hoursDiff / 24;

		if ($minutesDiff < 1) {
			return sprintf('%d seconds ago', round($secondsDiff));
		}
		if ($hoursDiff < 1) {
			return sprintf('%d minutes ago', round($minutesDiff));
		}
		if ($daysDiff < 1) {
			return sprintf('%d hours ago', round($hoursDiff));
		}

		return sprintf('%s', $date->format('Y-m-d, H:i'));
	}
}