<?php

	$event_guid = $vars["event_guid"];
	$question_guid = $vars["question_guid"];

	elgg_log("EVENT_MANAGER event_guid=$event_guid question_guid=$question_guid");	
	if($event_guid && ($entity = get_entity($event_guid))){
		// assume new question mode
		if(!($entity instanceof Event)){
			unset($entity);
		} else {
			if ($entity->canEdit() && $entity->access_id == ACCESS_PRIVATE) {
				$ia = elgg_set_ignore_access(true);
			}
		}
		
	//} elseif($question_guid && ($entity = get_entity($question_guid))) {
	} elseif ($question_guid ) {
		// Have to do this because of private event
		$ia = elgg_set_ignore_access(true);
		$entity = get_entity($question_guid);
		$associated_event = get_entity($entity->container_guid);
		// assume question edit mode and check access
		if(!($entity instanceof EventRegistrationQuestion) || ! $associated_event->canEdit()){
			unset($entity);
		}
	}
	
	if($entity instanceof EventRegistrationQuestion) {
		// assume day edit mode
		$guid 			= $entity->getGUID();
		$event_guid		= $entity->owner_guid;	
		$title 			= $entity->title;	
		$fieldtype		= $entity->fieldtype;
		$required		= $entity->required;
		$fieldoptions	= $entity->fieldoptions;
	} else {
		$event_guid	= $entity->getGUID();			
		$required = 1;
	}
	
	if($entity && $entity->canEdit()) {
		
		if(empty($title)){
			$title = elgg_echo('event_manager:editregistration:addfield:title');
		}
		
		$form_body = elgg_view('input/hidden', array('name' => 'event_guid', 'value' => $event_guid));
		$form_body .= elgg_view('input/hidden', array('name' => 'question_guid', 'value' => $question_guid));
		$form_body .= "<table class='elgg-table'><tr><td>";
		$form_body .= '<label>'.elgg_echo('event_manager:editregistration:question').'</label>';
		$form_body .= "</td><td>";
		$form_body .= elgg_view('input/text', array('name' => 'questiontext', 'value' => $title));
		$form_body .= "</td></tr><tr><td>";
		$form_body .= '<label>'.elgg_echo('event_manager:editregistration:fieldtype').'</label>';
		$form_body .= "</td><td>";
		$form_body .= elgg_view('input/dropdown', array('id' => 'event_manager_registrationform_question_fieldtype', 'value' => $fieldtype, 'name' => 'fieldtype', 'options' => array(
																				elgg_echo('event_manager:editregistration:addfield:field_type:textfield'),
																				elgg_echo('event_manager:editregistration:addfield:field_type:textarea'),
																				elgg_echo('event_manager:editregistration:addfield:field_type:dropdown'),
																				elgg_echo('event_manager:editregistration:addfield:field_type:radiobutton')
																								)));
		$form_body .= "</td></tr>";
		
		if(!in_array($fieldtype, array('Radiobutton', 'Dropdown', "Bouton d'options", 'Liste'))) {
			$displayNone = ' style="display:none;"';
		}
		
		$form_body .= '<tr id="event_manager_registrationform_select_options" '.$displayNone.'><td>';
		$form_body .= '<label>' . elgg_echo('event_manager:editregistration:fieldoptions') . '</label> ('.elgg_echo('event_manager:editregistration:commasepetared').')';
		$form_body .= "</td><td>";
		$form_body .= elgg_view('input/text', array('name' => 'fieldoptions', 'value' => $fieldoptions));
		$form_body .= "</td></tr><tr><td>&nbsp;</td><td>";
		$form_body .= elgg_view('input/checkboxes', array('name' => 'required', 'value' => $required, 'options' => array(elgg_echo('event_manager:registrationform:editquestion:required') => '1')));
		$form_body .= "</td></tr></table>";
		
		$form_body .= elgg_view('input/submit', array('value' => elgg_echo('submit')));
			
		$body = elgg_view('input/form', array(	'id' 	=> 'event_manager_registrationform_question',
												'name' 	=> 'event_manager_registrationform_question', 
												'action' 		=> 'javascript:event_manager_registrationform_add_field($(\'#event_manager_registrationform_question\'))',
												'body' 			=> $form_body));
		
		echo elgg_view_module("main",$title, $body, array('id' 	=> 'event_manager_registrationform_lightbox'));
		
	} else {
		echo elgg_echo("InvalidParameterException:GUIDNotFound", array($guid));
	}
	
if ($ia) {
	elgg_set_ignore_access($ia);
}
