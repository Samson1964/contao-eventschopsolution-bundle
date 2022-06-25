<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoEventschopsolutionBundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoEventschopsolutionBundle\EventListener;

//use Contao\Config;
//use Contao\Module;
//use Contao\PageModel;

class GetAllEventsListener
{

	public function onGetAllEvents(array $events, array $calendars, int $start, int $end, \Module $module): array
	{

		$this->KategorieFiltern($events);
		$this->SucheFiltern($events);
		$this->DatumFiltern($events);
		
		return $events;

	}

	private function KategorieFiltern(array &$events): void
	{

		$category = \Input::get('c'); // Kategorie aus URL holen

		if(!$category) return; // Keine Kategorie vorhanden, dann Events ungeändert zurückgeben

		// Klartexte der Kategorien einladen
		$kategorien = array();
		$resultSet = \Database::getInstance()->prepare('SELECT options FROM tl_catalog_fields WHERE fieldname=?')
		                                     ->execute('ext_category');
		if ($resultSet->options)
		{
			$_cat = unserialize($resultSet->options);
			foreach($_cat as $_c)
			{
				$kategorien[$_c['key']] = $_c['value'];
			}
		}

		$arrFilteredEvents = array();
		foreach($events as $key => $days)
		{
			foreach($days as $day => $eventday)
			{
				foreach($eventday as $i => $event)
				{
					//echo "|".$category."|".$event['ext_category']."|<br>";
					if($event['ext_category'] == $kategorien[$category])
					{
						// Nur Events der gewünschten Kategorie übernehmen
						$arrFilteredEvents[$key][$day][$i] = $event;
					}
				}
			}
		}
		$events = $arrFilteredEvents;
	}

	private function DatumFiltern(array &$events): void
	{

		$von = \Input::get('start'); // Kategorie aus URL holen
		$bis = \Input::get('end'); // Kategorie aus URL holen

		if(!$von && !$bis) return; // Keine Datum vorhanden, dann Events nicht ändern
		
		// Gewünschtes Datum umwandeln
		$von = mktime(0, 0, 0, (int)substr($von, 3, 2), (int)substr($von, 0, 2), (int)substr($von, 6, 4));
		$bis = mktime(23, 59, 59, (int)substr($bis, 3, 2), (int)substr($bis, 0, 2), (int)substr($bis, 6, 4));

		$arrFilteredEvents = array();
		foreach($events as $key => $days)
		{
			foreach($days as $day => $eventday)
			{
				foreach($eventday as $i => $event)
				{
					$datum = strtotime($event['datetime']);
					if($von <= $datum && $bis >= $datum)
					{
						$arrFilteredEvents[$key][$day][$i] = $event;
					}
				}
			}
		}
		$events = $arrFilteredEvents;

	}

	private function SucheFiltern(array &$events): void
	{
		$suchbegriff = \Input::get('q'); // Suchbegriff aus URL holen

		if(!$suchbegriff) return; // Kein Suchbegriff vorhanden, dann Events nicht ändern
		
		$arrFilteredEvents = array();
		foreach($events as $key => $days)
		{
			foreach($days as $day => $eventday)
			{
				foreach($eventday as $i => $event)
				{
					$gefunden = false;
					if(stristr($event['title'], $suchbegriff) == true) $gefunden = true;

					if(!$gefunden)
					{
						$teaser = strip_tags((string)$event['teaser']);
						if(stristr($teaser, $suchbegriff) == true) $gefunden = true;
					}

					if($gefunden) $arrFilteredEvents[$key][$day][$i] = $event;
				}
			}
		}
		$events = $arrFilteredEvents;
	}


}
