<?php
/**
 * River item footer
 */

// allow river views to override the response content
$responses = elgg_extract('responses', $vars, false);
if ($responses) {
        echo $responses;
        return true;
}       
   
$item = $vars['item'];
/* @var ElggRiverItem $item */
$object = $item->getObjectEntity();
$target = $item->getTargetEntity();


$user=elgg_get_logged_in_user_entity();
$canwrite = true;
if ($object->owner_guid != $object->container_guid) {
	$group = get_entity($object->container_guid);
	if ($group instanceof ElggGroup && !$group->isMember($user)) {
		$canwrite = false;
	}
}

// annotations and comments do not have responses
if ($item->annotation_id != 0 || !$object || elgg_instanceof($target, 'object', 'comment')) {
	return true;
}

$comment_count = $object->countComments();

if ($comment_count) {
	$comments = elgg_get_entities(array( 'type' => 'object', 'subtype' => 'comment', 'container_guid' => $object->getGUID(), 'limit' => 3, 'order_by' => 'e.time_created desc', 'distinct' => false,));
	$object_guid = $object->getGUID();
	// why is this reversing it? because we're asking for the 3 latest
	// comments by sorting desc and limiting by 3, but we want to display
	// these comments with the latest at the bottom.
	$comments = array_reverse($comments);

	if ($comment_count > count($comments)) {
		elgg_load_js('elgg.extra_feed_comments');
		$link = elgg_view('output/url', array(
			'href' => $object->getURL(),
			'onclick' => "elgg.extra_feed_comments(\"$object_guid\");return false;",
			'text' => elgg_echo('river:comments:all', array($comment_count)),
		));
		
		echo elgg_view_image_block(elgg_view_icon('speech-bubble-alt'), $link, array('class' => 'elgg-river-participation-'.$object_guid));
	}
	
	echo elgg_view_entity_list($comments, array('list_class' => 'elgg-river-comments-'.$object_guid, 'item_class' => 'elgg-river-participation', 'body_class' => $vars['body_class']));
	//echo elgg_view_annotation_list($comments, array('list_class' => 'elgg-river-comments-'.$object_guid, 'item_class' => 'elgg-river-participation', 'body_class' => $vars['body_class']));

}
/*
if ($object->canAnnotate(0, 'generic_comment')) {
	// inline comment form
?>
<?php
	//if ($canwrite) {
	$id = "comments-add-{$object->getGUID()}";
		echo elgg_view_form('comment/save', array(
			'id' => $id,
			'class' => 'elgg-river-participation elgg-form-small elgg-form-variable',
		), array('entity' => $object, 'inline' => true, 'id' => $id, 'canwrite' => $canwrite));
	//} else {
		//echo elgg_view_form('comments/add', array(
			//'id' => $id,
			//'class' => 'elgg-river-participation elgg-form-small',
		//), array('entity' => $object, 'inline' => true, 'id' => $id, 'canwrite' => $canwrite));
	//}
}
*/
