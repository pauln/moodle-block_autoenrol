<?php
class block_autoenrol extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_autoenrol');
    }

    function get_content() {
        global $COURSE, $USER;

        $context = context_course::instance($COURSE->id);
        if(is_guest($context) && !isguestuser()) {
            $instances = enrol_get_instances($COURSE->id, false);
            $selfenrol = enrol_get_plugin('self');
            $enrol = $this->get_selfenrol($instances);
            if (!$enrol) {
                $selfenrol->add_default_instance($COURSE);
                $instances = enrol_get_instances($COURSE->id, false);
                $enrol = $this->get_selfenrol($instances);
            }
            if (!$enrol) {
                    // Something went wrong - still no self-enrol plugin.
                return;
            }
            if ($enrol->status != ENROL_INSTANCE_ENABLED) {
                // Self-enrol plugin not enabled - enable it.
                $selfenrol->update_status($enrol, ENROL_INSTANCE_ENABLED);
            }

            // Role ID 5 is student.
            @$selfenrol->enrol_user($enrol, $USER->id, 5);
        }

        return $this->content;
    }

    function get_selfenrol($instances) {
        foreach ($instances as $id => $instance) {
            if ($instance->enrol === 'self') {
                return $instance;
            }
        }
        return false;
    }
}
?>