<?php

// Opportunity reminders (opportunity-reminders extension): its section of the
// Administration page and its settings page.

/** @var array<string, string> $mod_strings Set by SuiteCRM */
$mod_strings['LBL_OR_ADMIN_HEADER'] = 'Opportunity reminders';
$mod_strings['LBL_OR_ADMIN_HEADER_DESC'] = 'Follow-up tasks for opportunities that no longer move.';
$mod_strings['LBL_OR_ADMIN_TITLE'] = 'Opportunity reminders';
$mod_strings['LBL_OR_ADMIN_DESC'] = 'Set the delay before a reminder, run the reminders.';
$mod_strings['LBL_OR_PAGE_INTRO'] = 'Every morning at 7:00 (Administration → Schedulers), a "Follow up" task is created for the sales rep of each open opportunity whose record has not changed for this number of days. Never two: no new reminder while a task linked to the opportunity is not completed, nor before the same delay once it is.';
$mod_strings['LBL_OR_DAYS'] = 'Remind after (days without a change)';
$mod_strings['LBL_OR_DAYS_HELP'] = 'from 1 to 365, 30 by default';
$mod_strings['LBL_OR_SAVE'] = 'Save';
$mod_strings['LBL_OR_RUN'] = 'Save and run now';
$mod_strings['LBL_OR_SAVED'] = 'Setting saved: reminder after %d days without a change.';
$mod_strings['LBL_OR_RAN'] = 'Reminders run: %d task(s) created.';
$mod_strings['LBL_OR_DAYS_INVALID'] = 'The number of days must be a whole number from 1 to 365.';
