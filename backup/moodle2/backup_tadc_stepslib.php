<?php
/**
 * Define all the backup steps that will be used by the backup_url_activity_task
 *
 * @package    mod
 * @subpackage tadc
 * @copyright  2013 Talis Education Ltd.
 * @license    MIT
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete tadc structure for backup
 */

class backup_tadc_activity_structure_step extends backup_activity_structure_step {
    protected function define_structure() {
        // Define each element separated
        $tadc = new backup_nested_element('tadc', array('id'), array(
            'type', 'section_title', 'section_creator', 'start_page', 'end_page', 'container_title', 'document_identifier',
            'container_identifier', 'publication_date', 'volume', 'issue', 'publisher', 'needed_by',
            'edition', 'tadc_id', 'status_message', 'request_status', 'bundle_url', 'name', 'container_creator', 'reason_code',
            'other_response_data'
        ));


        // Define sources
        $tadc->set_source_table('tadc', array('id' => backup::VAR_ACTIVITYID));


        // Return the root element (tadc), wrapped into standard activity structure
        return $this->prepare_activity_structure($tadc);
    }
}