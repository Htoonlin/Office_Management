<?php
namespace ProjectManagement\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-11 10:45:57
 *
 * @package ProjectManagement\Helper
 */
class TaskHelper
{

    protected $dbAdapter = null;

    protected $form = null;

    protected $inputFilter = null;

    public function getForm()
    {
        if(!$this->form){
        	$form = new Form();
        	$taskId = new Element\Hidden('taskId');
        	$form->add($taskId);

        	$projectId = new Element\Select('projectId');
        	$projectId->setAttribute('class', 'form-control');
        	$projectId->setLabel('Project Id');
        	$form->add($projectId);

        	$name = new Element\Text('name');
        	$name->setAttribute('class', 'form-control');
        	$name->setLabel('Name');
        	$form->add($name);

        	$tag = new Element\Text('tag');
        	$tag->setAttribute('class', 'form-control');
        	$tag->setLabel('Tag');
        	$form->add($tag);

        	$level = new Element\Number('level');
        	$level->setAttributes(array(
        		'min' => '0',
        		'max' => '99999999999',
        		'step' => '1',
        	));
        	$level->setLabel('Level');
        	$form->add($level);

        	$managerId = new Element\Select('managerId');
        	$managerId->setAttribute('class', 'form-control');
        	$managerId->setLabel('Manager Id');
        	$form->add($managerId);

        	$fromTime = new Element\Date('fromTime');
        	$fromTime->setAttributes(array(
        		'allowPastDates' => true,
        		'momentConfig' => array('format' => 'YYYY-MM-DD'),
        	));
        	$fromTime->setLabel('From Time');
        	$form->add($fromTime);

        	$toTime = new Element\Date('toTime');
        	$toTime->setAttributes(array(
        		'allowPastDates' => true,
        		'momentConfig' => array('format' => 'YYYY-MM-DD'),
        	));
        	$toTime->setLabel('To Time');
        	$form->add($toTime);

        	$parentId = new Element\Select('parentId');
        	$parentId->setAttribute('class', 'form-control');
        	$parentId->setLabel('Parent Id');
        	$form->add($parentId);

        	$predecessorId = new Element\Select('predecessorId');
        	$predecessorId->setAttribute('class', 'form-control');
        	$predecessorId->setLabel('Predecessor Id');
        	$form->add($predecessorId);

        	$priority = new Element\Number('priority');
        	$priority->setAttributes(array(
        		'min' => '0',
        		'max' => '99999999999',
        		'step' => '1',
        	));
        	$priority->setLabel('Priority');
        	$form->add($priority);

        	$remark = new Element\Textarea('remark');
        	$remark->setAttribute('class', 'form-control');
        	$remark->setLabel('Remark');
        	$form->add($remark);

        	$current = new Element\Number('current');
        	$current->setLabel('Current');
        	$form->add($current);

        	$finished = new Element\Date('finished');
        	$finished->setAttributes(array(
        		'allowPastDates' => true,
        		'momentConfig' => array('format' => 'YYYY-MM-DD'),
        	));
        	$finished->setLabel('Finished');
        	$form->add($finished);

        	$status = new Element\Text('status');
        	$status->setAttribute('class', 'form-control');
        	$status->setLabel('Status');
        	$form->add($status);

        	$this->form = $form;
        }
        return $this->form;
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getInputFilter()
    {
        if(!$this->inputFilter){
        	$filter = new InputFilter();
        	$filter->add(array(
        		'name' => 'taskId',
        		'required' => true,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'projectId',
        		'required' => false,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'name',
        		'required' => true,
        		'filters' => array(
        			array('name' => 'StripTags'),
        			array('name' => 'StringTirm'),
        		),
        		'validators' => array(
        			array(
        				'name' => 'StringLength',
        				'max' => 250,
        				'min' => 1,
        				'encoding' => 'UTF-8',
        			),
        		),
        	));
        	$filter->add(array(
        		'name' => 'tag',
        		'required' => true,
        		'filters' => array(
        			array('name' => 'StripTags'),
        			array('name' => 'StringTirm'),
        		),
        		'validators' => array(
        			array(
        				'name' => 'StringLength',
        				'max' => 50,
        				'min' => 1,
        				'encoding' => 'UTF-8',
        			),
        		),
        	));
        	$filter->add(array(
        		'name' => 'level',
        		'required' => false,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'managerId',
        		'required' => true,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'fromTime',
        		'required' => true,
        	));
        	$filter->add(array(
        		'name' => 'toTime',
        		'required' => true,
        	));
        	$filter->add(array(
        		'name' => 'parentId',
        		'required' => false,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'predecessorId',
        		'required' => false,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'priority',
        		'required' => true,
        		'filters' => array(array('name' => 'Int')),
        	));
        	$filter->add(array(
        		'name' => 'remark',
        		'required' => false,
        		'filters' => array(
        			array('name' => 'StripTags'),
        			array('name' => 'StringTirm'),
        		),
        		'validators' => array(
        			array(
        				'name' => 'StringLength',
        				'max' => 500,
        				'min' => 1,
        				'encoding' => 'UTF-8',
        			),
        		),
        	));
        	$filter->add(array(
        		'name' => 'current',
        		'required' => true,
        		'filters' => array(array('name' => 'Float')),
        	));
        	$filter->add(array(
        		'name' => 'finished',
        		'required' => false,
        	));
        	$filter->add(array(
        		'name' => 'status',
        		'required' => true,
        		'filters' => array(
        			array('name' => 'StripTags'),
        			array('name' => 'StringTirm'),
        		),
        		'validators' => array(
        			array(
        				'name' => 'StringLength',
        				'max' => 1,
        				'min' => 1,
        				'encoding' => 'UTF-8',
        			),
        		),
        	));
        	$this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilter $filter)
    {
        $this->inputFilter = $filter;
    }


}