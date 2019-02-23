<?php
/**
 * @author      HR-IT-Solutions GmbH Florian Häusler <info@hr-it-solutions.com>
 * @copyright   Copyright (C) 2019 - 2019 HR-IT-Solutions GmbH
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

class plgSystemDD_AcyUnsubscribe extends JPlugin
{

	protected   $app;

	public function onAfterInitialise()
	{

		if ($this->app->isSite())
		{
			$input = $this->app->input;
			$subid = $input->get('subid');
			$option = $input->get('option');
			$ctrl = $input->get('ctrl');
			$task = $input->get('task');
			$mailid = $input->get('mailid', 0);
			$key = $input->get('key');

			if ($option !== 'com_acymailing' || $ctrl !== 'user' || $task !== 'out' || !$mailid)
			{
				return false;
			}

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('subid')
				->from($db->qn('#__acymailing_subscriber'))
				->where($db->qn('subid'). '=' . $db->q($subid))
				->where($db->qn('key'). '=' . $db->q($key));
			$db->setQuery($query);
			$isValidUser = $db->loadResult();

			if ($isValidUser)
			{

				$successmessage = $this->params->def('successmessage', 'Succesfull unsubscribed');

				$query = $db->getQuery(true);
				$query->update($db->qn('#__acymailing_listsub'))
					->set($db->qn('status') . '= ' . $db->q('-1'))
					->where(array($db->qn('subid') . ' = ' . $db->q($subid)));
				$db->setQuery($query);
				$db->execute();

				$successmessage = JText::_(strip_tags($successmessage));

				$this->app->redirect(JURI::base(), $successmessage);
			}
			else
			{
				return false;
			}
		}

		return false;

	}
}