<?php
/**
 * @author Rustam Ibragimov
 * @mail Rustam.Ibragimov@softline.ru
 * @date 24.06.13
 */
namespace Myzend\Controller;

set_time_limit(0);
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Dom\Query;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class MyzendController extends AbstractActionController
{
	protected $myzendTable;
	protected $actorsAll = array();
	protected $actorNames = array();
	protected $aActor = array();
	protected $aFilms = array();
	protected $films = array();
	protected $parseFilmsCount = 0;
	protected $parseActorsCount = 0;
	protected $allPages = 28140;
	protected $logger;

	public function indexAction()
	{
		$this->logger = new Logger();
		$writer = new Stream($_SERVER['DOCUMENT_ROOT'] . '/myparse/logs/newproject.log');
		$this->logger->addWriter($writer);
//		for($pageIndex = 10000;$pageIndex <= $this->allPages;$pageIndex = $pageIndex + 10)
//		{
//
//			$this->aFilms = $this->fetchAllFilmsToArray();
//			$this->aActor = $this->fetchAllActorsToArray();
//			$this->parseNewAdress($pageIndex);
//
//			if((($pageIndex % 100) == 0) || $pageIndex == $this->allPages)
//			{
//				$this->saveFilms();
//				$this->logger->log(Logger::INFO, 'Фильмы (' . $this->parseFilmsCount . ')');
//				$this->logger->log(Logger::INFO, 'Актеры (' . $this->parseActorsCount . ')');
//				$this->logger->log(Logger::INFO, 'Индекс (' . $pageIndex . ')');
//				$this->films = array();
//				$this->actorsAll = array();
//			}
//		}
		$html = 'http://video.ru/films/film/12865_Angelyi_nad_Brodveem';
		$html = file_get_contents($html);
		$dom = new Query($html);
		$results = $dom->execute('dt');
		foreach ($results as $result)
		{
			$title[] = $result->nodeValue;
			$href[] = $result->getAttribute('href');
		}
		echo '<pre>';
		var_dump($title);
		var_dump($href);
		echo '<pre>';		$this->aFilms = $this->fetchAllFilmsToArray();
		$this->aActor = $this->fetchAllActorsToArray();

		return new ViewModel(array(
			'aFilms' => count($this->aFilms),
			'aActor' => count($this->aActor),
		));
	}

	public function getMyzendTable()
	{
		if (!$this->myzendTable) {
			$sm = $this->getServiceLocator();
			$this->myzendTable = $sm->get('Myzend\Model\MyzendTable');
		}
		return $this->myzendTable;
	}

	protected function fetchAllFilmsToArray()
	{
		$oFilms = $this->getMyzendTable()->fetchAll('films');
		foreach($oFilms as $film)
		{
			$this->aFilms[$film->href] = array(
				'title' => $film->title,
				'href' => $film->href,
				'img' => $film->img,
				'actors' => explode(',', $film->actors),
				'description' => $film->description,
			);
		}
		return $this->aFilms;
	}

	protected function fetchAllActorsToArray()
	{
		$oActors = $this->getMyzendTable()->fetchAll('actors');
		foreach($oActors as $actor)
		{
			$this->aActor[$actor->url] = array(
				'title' => $actor->title,
				'url' => $actor->url,
			);
		}
		return $this->aActor;
	}

	protected function parseNewAdress($pageIndex)
	{
		$imgHref = '';
		$tab = '';
		$html = 'http://video.ru/films/mainpage/index/rating/0/0/0/0/' . $pageIndex;
		$html = file_get_contents($html);
		$dom = new Query($html);
		$results = $dom->execute('.title a');
		foreach ($results as $result)
		{
			$title = $result->nodeValue;
			$href = $result->getAttribute('href');
			if(!array_key_exists($href, $this->aFilms))
			{
				$html = file_get_contents('http://video.ru' . $href);
				$dom = new Query($html);
				$imgHrefs = $dom->execute('.img a img');
				$actors = $dom->execute('.film-actors a');
				$tables = $dom->execute('table');
				foreach($tables as $table)
				{
					foreach($table->childNodes as $k => $tabl)
					{
						if($k == 3)
						{
							$tab = $tabl->nodeValue;
							$tab = str_replace('Описание', '',$tab);
							$tab = trim($tab);
						}
					}
				}
				foreach ($imgHrefs as $imgHref)
				{
					$imgHref = $imgHref->getAttribute('src');
				}
				foreach ($actors as $actor)
				{
					$this->actorNames[] = $actor->nodeValue;
					$actorHref = $actor->getAttribute('href');
					if(!array_key_exists($actorHref, $this->aActor))
					{
						$this->logger->log(Logger::INFO, 'Запись в таблицу актера ' . $actor->nodeValue);
						$this->getMyzendTable()->addOne('actors', array('title' => $actor->nodeValue, 'url' =>$actorHref));
						$this->parseActorsCount++;
					} else {
						$this->logger->log(Logger::INFO, 'Такой актер уже есть в БД (' . $actor->nodeValue . ')');
					}
				}
				$this->films[] = array('title' => $title, 'href' => $href, 'img' => $imgHref, 'actors' => implode(',', $this->actorNames), 'description' => $tab);
			} else {
				$this->logger->log(Logger::INFO, 'Такой фильм уже есть в БД (' . $title . ')');
			}
			$this->actorNames = array();
		}
	}

	protected function saveFilms()
	{
		foreach($this->films as $film)
		{
				$this->logger->log(Logger::INFO, 'Запись в таблицу фильма ' . $film['title']);
				$this->getMyzendTable()->addOne('films', $film);
				$this->parseFilmsCount++;
		}
	}
}