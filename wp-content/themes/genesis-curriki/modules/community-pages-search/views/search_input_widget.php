<?php 

global $search;

$current_language = "eng";
if( defined('ICL_LANGUAGE_CODE') )
    $current_language = cur_get_current_language(ICL_LANGUAGE_CODE); 

$language = "";
if($current_language !== "eng")
{
    $language = $current_language;
}
    
?>
<div class="search-bar grid_12">
    <span class="search-widget">
        <div class="search-tabs">

            <!-- Search Tabs-->
            <a class="resource-tab tab rounded-borders-top <?php echo (!isset($search->request['type']) || $search->request['type'] == 'Resource') ? 'selected' : ''; ?>" href="<?php echo $search->resourcesTabURL; ?>" target="_top"><span class="tab-icon fa fa-book strong"></span><span class="tab-text"><strong><?php echo __('Resources','curriki'); ?> </strong></span></a>
            <!-- /Search Tabs-->

            <div class="search-tool-tip-text" style="display:none">
                <Strong><?php echo __('For clearer results, try some of these easy techniques in the search entry box.','curriki'); ?></strong>
                <ol style="list-style: disc">
                    <li><?php echo __('By default, when AND,OR,NOT or comma or + / - / ? / * are not included in the search text we search for the complete phrase entered.','curriki'); ?></li>
                    <li><?php echo __('You can use double quotes to search for an exact phrase, as in "The Sun Also Rises" or "George Washington" and combine this with other operators.','curriki'); ?></li>
                    <li><?php echo __("A comma also acts as an OR. 'George, Washington' will return resources that contain either 'George' or 'Washington'.",'curriki'); ?></li>
                    <li><?php echo __("You can use an AND if searching for both words.  George AND Washington will return resources that contain BOTH 'George' and 'Washington'.  You must use all caps as in 'AND'.",'curriki'); ?></li>
                    <li><?php echo __("Not sure of the exact word? Crop it to a shorter form and use an asterisk (*). For example, Read* will return Reads, Reader, Reading, etc.",'curriki'); ?></li>
                    <li><?php echo __("Not sure of the spelling? Use a question mark (?) to replace a single letter. For example, Read? will return Reads, Ready, etc. You can even use multiple questions marks. For example, P??r will return Pour, Poor, Pear, Peer, etc.",'curriki'); ?></li>
                    <li><?php echo __('Not happy with crossover results? Reduce the number of returns by using a minus symbol (-). For example, "George Washington" -Carver will remove any returns with the word Carver.','curriki'); ?></li>
                    <li><?php echo __('Use the word "OR" to broaden a search. For example, England OR Britain will find resources with either one, but not necessarily both.','curriki'); ?></li>
                    <li><?php echo __('Or, you can combine any of the above.','curriki'); ?> </li>
                </ol>
            </div>
            <!-- /Search Tips-->
        </div>
        <input type="hidden" name="type" id="type" value="<?php echo isset($search->request['type']) ? stripslashes(htmlspecialchars($search->request['type'])) : 'Resource'; ?>"/>
        <!-- Search Input field-->
        <div class="search-input">
            <div class="search-field">
                <!--<input type="hidden" name="sort" value="topofsearch desc,partner desc,reviewrating desc,memberrating desc" />-->
                <input class="rounded-borders-left" placeholder="<?php echo __('Start Searching','curriki'); ?>" type="text" name="phrase" value="<?php if (isset($search->request['phrase'])) echo stripslashes(htmlspecialchars($search->request['phrase'])); ?>" >
            </div>
            <div class="search-button"><button type="submit" class="rounded-borders-right" ><span class="search-button-icon fa fa-search"></span><span class="search-text"><?php echo __('Search','curriki'); ?></span></button></div>
        </div>
        <!-- /Search Input field-->

        <!-- Advance Options -->
        <span id="resources-tab" class="tab-container">
            <div class="search-options rounded-borders-bottom border-grey">

<!--                <select name="seachin" id="language" class="search-dropdown" ng-change="setLanguage()" style="margin-bottom: 0px; height: 25px !important">
                    <option value="site"><?php echo __('Search Entire Site','curriki'); ?></option>
                    <option value="community"><?php echo __('Search Community Resources','curriki'); ?></option>
                </select>-->


                <div style="clear:both"></div>


                <?php get_template_part('modules/community-pages-search/views/search_advance_options_slide'); ?>
                <?php get_template_part('modules/community-pages-search/views/search_by_standards_slide'); ?>

            </div>
        </span>
        <!-- /Advance Options -->

        <!-- Hidden Fields -->
        
        <input type="hidden" name="start" id="start" value="<?php echo isset($search->request['start']) ? stripslashes(htmlspecialchars($search->request['start'])) : "0"; ?>"/>
        <input type="hidden" name="partnerid" id="partnerid" value="<?php echo $search->partnerid ? stripslashes(htmlspecialchars($search->partnerid)) : '1'; ?>" />
        <input type="hidden" name="searchall" id="searchall" value="<?php echo isset($search->request['searchall']) ? stripslashes(htmlspecialchars($search->request['searchall'])) : ""; ?>"/>
        <input type="hidden" name="viewer" id="viewer" value="<?php echo isset($search->request['viewer']) ? stripslashes(htmlspecialchars($search->request['viewer'])) : ""; ?>"/>
        <?php
        if (isset($search->request['search_target']) AND $search->request['search_target'] == 'curriki')
            $search->branding = 'curriki';
        ?>
        <input type="hidden" name="branding" id="branding" value="<?php echo stripslashes(htmlspecialchars($search->branding)); ?>"/>
        <?php if(!isset($search->request['sort'])): ?>
            <input type="hidden" name="sort" value="<?php echo isset($search->request['sort']) ? stripslashes(htmlspecialchars($search->request['sort'])) : 'rank1 desc'; ?>"/>
        <?php endif; ?>
        <!-- /Hidden Fields -->

    </span>
</div>