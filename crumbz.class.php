<?php
/**
 *  File		crumbz.class.php (requires MODx Revolution 2.x)
 * Created on	Jul 02, 2010
 * Project		shawn_wilkerson
 * @package 	crumbz
 * @version	1.0
 * @category	navigation
 * @author		W. Shawn Wilkerson
 * @link		http://www.shawnWilkerson.com
 * @copyright  Copyright (c) 2010, W. Shawn Wilkerson.  All rights reserved.
 * @license  	GPL
 *
 *
 * This is the main file to access the breadcrumb class.
 *
 */

class Crumbz
{
	function __construct(modX & $modx, array $config= array ())
	{
		/*
		 * Import $modx reference
		 */
		$this->modx= & $modx;
		
		/*
		 * establish site start
		 */
		$this->siteStartID = $this->modx->getOption('site_start');
		
		/*
		 * Establish current resource ID 
		 */
		$this->docID = $this->modx->resource->get('id');

		/*
		 * Establish base settings -- merging incoming overrides
		 */
		$this->_config= array_merge(array (
			'id' => $this->docID,
			'depth' => 10,
			'format' => 'bar',
			'seperator' => ' &raquo; ',
			'textOnly' => false,
			'removeSiteHome' => false,
			'removeLastID' => false,
			'lastChildAsLink' => false,
			'useLongTitle' => false,

			
		), $config);

		/*
		 * establish parents of user provided document
		 */
		$this->parents= $this->modx->getParentIds($this->_config['id'], $this->_config['depth']);

		/*
		 * Reverse array to top-down order
		 */
		$this->parents= array_reverse($this->parents);
	}

	/**
	 * clear resources
	 */
	function __destruct()
	{

		unset ($this->modx, $this->_config, $this->parents, $this->setHome, $this->lastCrumb, $this->getNavigation, $this->links, $this->setFormat);
	}

	/**
	 * Processes the request
	 * @return html formated string of navigation links
	 */
	public function run()
	{
		$this->setHome();
		$this->parents = $this->setLastCrumb();
		$this->links= $this->getNavigation();
		return $this->setFormat();
	}

	/**
	 * Gets all the resource id's in the current path or of docID provided
	 * @return navigation links for current page
	 */
	public function getNavigation()
	{
		$useText= ($this->_config['useLongTitle'] == true) ? 'longtitle' : 'pagetitle';
		foreach ($this->parents as $resourceID)
		{
			$obj= $this->modx->getObject('modDocument', array (
				'id' => $resourceID
			));

			/*
			 * Stop processing on deleted documents as the tree will also be deleted
			 */
			if ($obj->get('deleted') == false)
			{

				/*
				 * Show only published documents and those not hidden from menus
				 */
				if (($obj->get('published') == true) && ($obj->get('hidemenu') == false))
				{

					/*
					 * Capture text to be used for link / text
					 */
					$linkText= $obj->get($useText);

					/*
					 * Match the title text to the repective linkText.
					 * pageTitle gets longTitle as Title="" text
					 * longTitle gets description as Title="" text
					 */
					$linkTitle= ($this->_config['useLongTitle'] == true) ? $obj->get('description') : $obj->get('longtitle');
					$o[]= ($this->_config['textOnly'] == true) ? $linkText : '<a href="'.$this->modx->makeUrl($resourceID).'" title="'.$linkTitle.'">'.$linkText.'</a>';
				}
			}
		}

		/*
		 * Replace the last link with straight text
		 */
		if ($this->_config['lastChildAsLink'] == true)
		{
			array_pop($o);
			$o[]= $linkText;
		}

		/*
		 * clear resources
		 */
		unset ($useText, $resourceID, $obj, $linkText, $linkTitle);
		return $o;
	}

	/**
	 * Sets the format of the breadcrumbs to be returned.
	 * @return string of html content.
	 */
	public function setFormat()
	{
		$c= 0;
		$numLinks= count($this->links);
		foreach ($this->links as $location)
		{
			switch ($this->_config['format'])
			{
				case 'list' :
					$o .= '<li>'.$location.'</li>';
					break;

				case 'vertical' :
					$o .= $location.'<br />';
					break;

				case 'bar' :
				default :
					$o .= $location;
					$o .= ($numLinks === ++ $c) ? '' : $this->_config['seperator'];
					break;
			}
		}
		return $o;
	}
	/**
	 * Establishes the current site home and adjusts the $this->parents array
	 * Based on the removeSiteHome parameter: removes the array key.
	 * Or sets the key to the site_start, or leaves it as currently set in situations a subdoc is the main doc
	 * @return int | empty .
	 */
	public function setHome()
	{
		if ($this->_config['removeSiteHome'] == true)
		{
			array_shift($this->parents);
		} else
		{
			$this->parents[0]= ($this->parents[0] == 0) ? $this->siteStartID : $this->parents[0];
		}
	}

	/**
	 * Based on the removeLastChild parameter
	 * @returns array filtered array key of $this->parents removing duplicates
	 */
	public function setLastCrumb()	
	{
		$this->parents[] = ($this->_config['removeLastID'] == false) ? $this->_config['id'] : '';
		return array_unique($this->parents);		
	}
}
?>