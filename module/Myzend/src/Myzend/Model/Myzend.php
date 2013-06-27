<?php
/**
 * @author Rustam Ibragimov
 * @mail Rustam.Ibragimov@softline.ru
 * @date 25.06.13
 */
namespace Myzend\Model;

class Myzend
{
	public $id;
	public $title;
	public $desc_k;
	public $desc;
	public $logo;

	public function exchangeArray($data)
	{
		$this->id     = (isset($data['id'])) ? $data['id'] : null;
		$this->title = (isset($data['title'])) ? $data['title'] : null;
		$this->desc_k  = (isset($data['desc_k'])) ? $data['desc_k'] : null;
		$this->desc  = (isset($data['desc'])) ? $data['desc'] : null;
		$this->logo  = (isset($data['logo'])) ? $data['logo'] : null;
	}
}