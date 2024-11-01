<?php 

$topics = $this->get_topics();
$better_topics = array();
foreach($topics as $topic){
    $better_topics[$topic->Category][] = $topic;
}
$saved_topics = get_post_meta(get_the_ID(),'wotb_topics',true);
if(!is_array($saved_topics)){
    $saved_topics = array();
}

$saved_places = get_post_meta(get_the_ID(),'wotb_places',true);
if(!is_array($saved_places)){
    $saved_places = array();
}

?>
<i>Make it easier for readers to find your post by selecting topics and places for this post</i><br/>

<table class="form-table">
    <tbody>
        <?php foreach($better_topics as $key => $new_topics){ ?>
        <tr>
            <th scope="row">
                <label for="wotb_topics"><?php echo $key; ?></label>
            </th>
            <td>
                <ul>
                    <?php foreach($new_topics as $new_topic){
                        $comment_tag = $new_topic->CommentTag;
                     ?>
                        <?php if($new_topic->CategoryType === 'RadioButton'){ ?>
                            <li>
                                <input <?php if(in_array($comment_tag,$saved_topics)){echo 'checked="checked"';} ?> id="<?php echo $comment_tag; ?>" name="wotb_topics[]" value="<?php echo $comment_tag; ?>" type="radio">
                                <label for="<?php echo $comment_tag; ?>"><?php echo $new_topic->TopicTitle; ?></label>
                            </li>
                        <?php }elseif($new_topic->CategoryType === 'Checkbox'){ ?>
                            <li>
                                <input <?php if(in_array($comment_tag,$saved_topics)){echo 'checked="checked"';} ?> id="<?php echo $comment_tag; ?>" name="wotb_topics[]" value="<?php echo $comment_tag; ?>" type="checkbox">
                                <label for="<?php echo $comment_tag; ?>"><?php echo $new_topic->TopicTitle; ?></label>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

            </td>
        </tr>
        <?php } ?>
        <tr>
            <th scope="row">
                <label for="wotb_places">Places (max of 3)</label>
            </th>
            <td>
                <select name="wotb_places[]" id="wotb_places" multiple="multiple" class="regular-text">
                    <?php if(!empty($saved_places) && is_array($saved_places)){
                        foreach($saved_places as $saved_place){
                            $new = explode(':',$saved_place);
                     ?>
                        <option selected="selected" value="<?php echo $saved_place; ?>"><?php echo $new[0]; ?></option>
                 <?php }} ?>
                </select>
            </td>
        </tr>
        
    </tbody>
</table>