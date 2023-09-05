<?php

/**
* Модель для роботи з мультимовністю
*/
class wl_language_model
{
	
	private $add_alias_position = array();
	private $words = array();
	private $words_aliases = array(0);
	private $i18n = null;

	public function get($word, $alias_id = -1)
	{
		if(substr($word, 0, 1) == '-')
			return substr($word, 1);

		if(empty($this->words))
			$this->getWords($alias_id);
		if($alias_id > 0 && !in_array($alias_id, $this->words_aliases))
			$this->getWords($alias_id);

		if(array_key_exists($word, $this->words))
		{
			if($this->words[$word] != '')
				return $this->words[$word];
			return $word;
		}
		return $this->add($word, $alias_id);
	}

	public function add($word, $alias_id = -1)
	{
		if(is_null($this->i18n))
		{
			if(LANGUAGE)
				foreach (ALL_LANGUAGES as $language) {
					if(file_exists(APP_PATH . 'i18n' . DIRSEP . $language . '.php'))
					{
						require APP_PATH . 'i18n' . DIRSEP . $language . '.php';
						$this->i18n[$language] = ${$language};
					}
					else
						$this->i18n[$language] = [];
				}
			else
			{
				if(file_exists(APP_PATH . 'i18n' . DIRSEP . 'uk.php'))
				{
					require APP_PATH . 'i18n' . DIRSEP . 'uk.php';
					$this->i18n = $uk;
				}
				else
					$this->i18n = [];
			}
		}

		$data['word'] = $this->words[$word] = $word;
		if(!LANGUAGE)
			$data['value'] = $this->words[$word] = $this->i18n[$word] ?? '';
		$data['alias_id'] = $this->alias->id;
		$data['type'] = 1;
		if($alias_id >= 0)
			$data['alias_id'] = $alias_id;
		if(empty($this->add_alias_position[$data['alias_id']]))
			$this->add_alias_position[$data['alias_id']] = $this->db->getCount('wl_language_words', $data['alias_id'], 'alias_id');
		$data['position'] = ++$this->add_alias_position[$data['alias_id']];
		if($word_id = $this->db->insertRow('wl_language_words', $data))
		{
			$insert_data = [];
			if(LANGUAGE)
				foreach (ALL_LANGUAGES as $language) {
					$value = '';
					if(!empty($this->i18n[$language][$word]))
						$value = $this->words[$word] = $this->i18n[$language][$word];
					$insert_data[] = array('language' => $language, 'value' => $value);
				}
			if(!empty($insert_data))
				$this->db->insertRows('wl_language_values', ['word_id' => $word_id, 'language', 'value'], $insert_data);
			$this->clearCache($alias_id);
		}
		return $this->words[$word];
	}

	public function getAllWords()
	{
		$this->db->select('wl_language_words as w');
		if(LANGUAGE)
			foreach (ALL_LANGUAGES as $language) {
				$this->db->join("wl_language_values as language_{$language}", "value as {$language}", array('language' => $language, 'word' => '#w.id'));
			}
		else
			$this->db->join("wl_language_values", "value", array('word' => '#w.id'));
		$this->db->order('position');
		return $this->db->get('array');
	}

	private function getWords($alias_id = -1)
	{
		$cache_alias = false;
		if(empty($this->alias->alias))
			$cache_alias = 'wl_aliases';
		if($alias_id == -1 || empty($this->words))
	        if($cache = $this->cache->get('textWords', $cache_alias))
			{
				$this->words = $cache['words'];
				$this->words_aliases = $cache['words_aliases'];
				if($alias_id == -1 || in_array($alias_id, $this->words_aliases))
					return true;
			}
		if(!in_array($this->alias->id, $this->words_aliases))
			$this->words_aliases[] = $this->alias->id;
		if($alias_id > 0 && !in_array($alias_id, $this->words_aliases))
			$this->words_aliases[] = $alias_id;
		if($alias_id == -1 || empty($this->words))
			$where['alias_id'] = $this->words_aliases;
		else
			$where['alias_id'] = $alias_id;

		$this->db->select('wl_language_words as w', 'id, alias_id, word', $where)
					->join('wl_language_values', 'value', $this->data->array_language(['word_id' => '#w.id']))
					->order('alias_id');
		if($words = $this->db->get('array'))
			foreach ($words as $word) {
				$word->word = trim($word->word);
				if(array_key_exists($word->word, $this->words))
				{
					$this->db->deleteRow('wl_language_words', $word->id);
					$this->db->deleteRow('wl_language_values', $word->id, 'word_id');
				}
				else
					$this->words[$word->word] = $word->value;
			}
		$this->cache->add('textWords', ['words_aliases' => $this->words_aliases, 'words' => $this->words], $cache_alias);
		return true;
	}

	public function save($word, $language = false, $value = '', $rewrite = true)
	{
		$where['word'] = $word;
		if($language)
			$where['language'] = $language;
		if($translate = $this->db->getAllDataById('wl_language_values', $where))
		{
			if($rewrite || $translate->value == '')
				$this->db->updateRow('wl_language_values', array('value' => $value), $translate->id);
		}
		else
		{
			$where['value'] = $value;
			$this->db->insertRow('wl_language_values', $where);
		}
		if($rewrite)
			if($word = $this->db->getAllDataById('wl_language_words', $word))
				$this->clearSessionCacheForAlias($word->alias_id);
		return true;
	}

	public function copy($alias_id, $language = false)
	{
		if($words = $this->db->getAllDataByFieldInArray('wl_language_words', $alias_id, 'alias_id'))
			foreach ($words as $word) {
				$this->save($word->id, $language, $word->word, false);
			}
		$this->clearSessionCacheForAlias($alias_id);
		return true;
	}

	private $wl_aliases = false;
	private function clearCache($alias_id = 0)
	{
		if($alias_id > 0 && $alias_id == $this->alias->id)
			$this->cache->delete('textWords');
		elseif(empty($this->wl_aliases))
		{
			$this->wl_aliases = $this->db->select('wl_aliases', 'alias as uri')->get('array');
			foreach ($this->wl_aliases as $alias) {
				$this->cache->delete('textWords', $alias->uri);
			}
		}
		return true;
	}

}

?>