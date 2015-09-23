<?php
// Avoid direct access to this piece of code
if (!function_exists('add_action') || (!empty($_POST) && !check_admin_referer('maintenance_wp_slimstat','maintenance_wp_slimstat_nonce'))){
	exit(0);
}

include_once(dirname(dirname(__FILE__))."/view/wp-slimstat-reports.php");
wp_slimstat_reports::init();

if (!empty($_REQUEST['action'])){
	switch ($_REQUEST['action']){
		case 'activate-indexes':
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats ADD INDEX {$GLOBALS['wpdb']->prefix}stats_resource_idx(resource(20))");
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats ADD INDEX {$GLOBALS['wpdb']->prefix}stats_browser_idx(browser(10))");
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats ADD INDEX {$GLOBALS['wpdb']->prefix}stats_searchterms_idx(searchterms(15))");
			wp_slimstat_admin::show_alert_message(__('Congrats! Slimstat is now optimized for <a href="http://www.youtube.com/watch?v=ygE01sOhzz0" target="_blank">ludicrous speed</a>.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'deactivate-indexes':
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats DROP INDEX {$GLOBALS['wpdb']->prefix}stats_resource_idx");
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats DROP INDEX {$GLOBALS['wpdb']->prefix}stats_browser_idx");
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats DROP INDEX {$GLOBALS['wpdb']->prefix}stats_searchterms_idx");
			wp_slimstat_admin::show_alert_message(__('Indexing has been disabled. Enjoy the extra database space!','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'delete-records':
			$rows_affected = 0;

			if (key_exists($_REQUEST['f'], wp_slimstat_db::$columns_names)){
				$rows_affected = wp_slimstat::$wpdb->query("
					DELETE t1.* 
					FROM {$GLOBALS['wpdb']->prefix}slim_stats t1
					WHERE ".wp_slimstat_db::get_combined_where('', '*', false));
			}
			wp_slimstat_admin::show_alert_message(intval($rows_affected).' '.__('records deleted from your database.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'delete-maxmind':
			@unlink( wp_slimstat::$maxmind_path );
			wp_slimstat_admin::show_alert_message(__('The geolocation database has been uninstalled from your server.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'download-maxmind':
			$error = wp_slimstat::download_maxmind_database();

			if (!empty($error)){
				wp_slimstat_admin::show_alert_message($error, 'wp-ui-notification below-h2');
			}
			else {
				wp_slimstat_admin::show_alert_message(__('The geolocation database has been installed on your server.','wp-slimstat'), 'wp-ui-highlight below-h2');
			}
			break;

		case 'import-settings':
			$new_options = @unserialize(stripslashes($_POST['import-slimstat-settings']));
			$new_options = array_intersect_key($new_options, wp_slimstat::$options);
			if (!empty($new_options)){
				foreach ($new_options as $a_option_name => $a_option_value){
					wp_slimstat::$options[$a_option_name] = $a_option_value;
				}
			}
			break;

		case 'import-data-from-old-tables':
			$current_id = wp_slimstat::$wpdb->get_var( "SELECT COUNT(*) FROM  {$GLOBALS['wpdb']->prefix}slim_stats" );
			wp_slimstat::$wpdb->query( "
				INSERT INTO {$GLOBALS['wpdb']->prefix}slim_stats (
					id,
					ip,
					other_ip,
					username,
					country,
					referer,
					resource,
					searchterms,

					plugins,
					notes,
					visit_id,
					server_latency,
					page_performance,

					browser,
					browser_version,
					browser_type,
					platform,
					language,
					user_agent,

					screen_width,
					screen_height,

					content_type,
					category,
					author,
					content_id,

					outbound_resource,

					dt
				)
				SELECT 
					t1.id + $current_id + 1,
					t1.ip,
					t1.other_ip,
					NULLIF(t1.user, ''),
					NULLIF(t1.country, ''),
					NULLIF(t1.referer, ''),
					NULLIF(t1.resource, ''),
					NULLIF(t1.searchterms, ''),
					NULLIF(t1.plugins, ''),
					NULLIF(t1.notes, ''),
					t1.visit_id,
					t1.server_latency,
					t1.page_performance,

					NULLIF(tb.browser, ''),
					NULLIF(tb.version, ''),
					tb.type,
					NULLIF(tb.platform, ''),
					NULLIF(t1.language, ''),
					NULLIF(tb.user_agent, ''),

					9812,
					9812,

					NULLIF(tci.content_type, ''),
					NULLIF(tci.category, ''),
					NULLIF(tci.author, ''),
					tci.content_id,

					NULL,

					t1.dt

				FROM {$GLOBALS['wpdb']->prefix}slim_stats_3 AS t1
				INNER JOIN {$GLOBALS['wpdb']->base_prefix}slim_browsers AS tb ON t1.browser_id = tb.browser_id
				INNER JOIN {$GLOBALS['wpdb']->base_prefix}slim_content_info AS tci ON t1.content_info_id = tci.content_info_id" );

			// Copy the events
			wp_slimstat::$wpdb->query( "
				INSERT INTO {$GLOBALS['wpdb']->prefix}slim_events (
					type,
					event_description,
					notes,
					position,
					id,
					dt
				)
				SELECT
					tob.type,
					SUBSTRING(tob.notes, LOCATE('Event:', tob.notes)+6, LOCATE(',', tob.notes, LOCATE('Event:', tob.notes)+6) - LOCATE('Event:', tob.notes)-6),
					SUBSTRING(tob.notes, 1, LOCATE('Event:', tob.notes) - 3),
					tob.position,
					tob.id + $current_id + 1,
					tob.dt
				FROM {$GLOBALS['wpdb']->prefix}slim_outbound AS tob" );
			wp_slimstat_admin::show_alert_message(__('Your data was successfully imported. You may now drop the old tables: wp_slim_stats_3, wp_slim_browsers, wp_slim_content_info, wp_slim_screenres, wp_slim_outbound. Please note: if you are using Slimstat in a MU network, you will need to run the import script on all your sites before you can delete the old tables.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'restore-views':
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%meta-box-order_admin_page_wp-slim-view-%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%meta-box-order_slimstat_page_wp-slim-view-%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%mmetaboxhidden_admin_page_wp-slim-view-%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%mmetaboxhidden_slimstat_page_wp-slim-view-%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%meta-box-order_slimstat%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%metaboxhidden_slimstat%'");
			$GLOBALS['wpdb']->query("DELETE FROM {$GLOBALS['wpdb']->prefix}usermeta WHERE meta_key LIKE '%closedpostboxes_slimstat%'");
			wp_slimstat_admin::show_alert_message(__('Your reports were successfully restored to their default arrangement.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'switch-engine':
			$have_innodb = wp_slimstat::$wpdb->get_results("SHOW VARIABLES LIKE 'have_innodb'", ARRAY_A);
			if ($have_innodb[0]['Value'] != 'YES') return;

			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_stats ENGINE = InnoDB");
			wp_slimstat::$wpdb->query("ALTER TABLE {$GLOBALS['wpdb']->prefix}slim_events ENGINE = InnoDB");

			wp_slimstat_admin::show_alert_message(__('Your Slimstat tables have been successfully converted to InnoDB.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'truncate-archive':
			wp_slimstat::$wpdb->query("DELETE tsa FROM {$GLOBALS['wpdb']->prefix}slim_stats_archive tsa");
			wp_slimstat::$wpdb->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_stats_archive");
			wp_slimstat_admin::show_alert_message(__('All the archived records were successfully deleted.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		case 'truncate-table':
			wp_slimstat::$wpdb->query("DELETE te FROM {$GLOBALS['wpdb']->prefix}slim_events te");
			wp_slimstat::$wpdb->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_events");
			wp_slimstat::$wpdb->query("DELETE t1 FROM {$GLOBALS['wpdb']->prefix}slim_stats t1");
			wp_slimstat::$wpdb->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_stats");
			wp_slimstat_admin::show_alert_message(__('All the records were successfully deleted.','wp-slimstat'), 'wp-ui-highlight below-h2');
			break;

		default:
			break;
	}
}

// Retrieve some information about the tables used by Slimstat
$check_index = wp_slimstat::$wpdb->get_results("SHOW INDEX FROM {$GLOBALS['wpdb']->prefix}slim_stats WHERE Key_name = 'stats_resource_idx'");
$details_wp_slim_tables = array_merge(
	wp_slimstat::$wpdb->get_results("SHOW TABLE STATUS LIKE '{$GLOBALS['wpdb']->prefix}slim_stats'", ARRAY_A),
	wp_slimstat::$wpdb->get_results("SHOW TABLE STATUS LIKE '{$GLOBALS['wpdb']->prefix}slim_events'", ARRAY_A),
	wp_slimstat::$wpdb->get_results("SHOW TABLE STATUS LIKE '{$GLOBALS['wpdb']->prefix}slim_stats_archive'", ARRAY_A)
);
$have_innodb = wp_slimstat::$wpdb->get_results("SHOW VARIABLES LIKE 'have_innodb'", ARRAY_A);
$suffixes = array('bytes', 'KB', 'MB', 'GB', 'TB');
?>

<table class="form-table widefat">
<tbody>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('Debugging','wp-slimstat') ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Tracker Error Code','wp-slimstat') ?></th>
		<td>
			<?php echo is_array(wp_slimstat::$options['last_tracker_error'])?'<code>'.wp_slimstat::$options['last_tracker_error'][0].' '.wp_slimstat::$options['last_tracker_error'][1].'</code> '.__('recorded on','wp-slimstat').' '.date_i18n(wp_slimstat::$options['date_format'], wp_slimstat::$options['last_tracker_error'][2], true).' @ '.date_i18n(wp_slimstat::$options['time_format'],  wp_slimstat::$options['last_tracker_error'][2], true):__('No Errors so far','wp-slimstat'); ?>
			<span class="description"><?php _e('The information here above is useful to troubleshoot issues with the tracker. Please include this code when sending a support request.','wp-slimstat') ?></span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('Layout','wp-slimstat') ?></td>
	</tr>
	<tr>
		<th scope="row"><a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=restore-views" onclick="return(confirm('<?php _e('Are you sure you want to restore the default arrangement of your reports?','wp-slimstat'); ?>'))"><?php _e('No Panic Button','wp-slimstat'); ?></a></th>
		<td>
			<span class="description"><?php _e("Reset the default arrangement of your reports. Helpful when, for some reason, reports disappear from your panels or something doesn't look right in your views.",'wp-slimstat') ?></span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('Data Maintenance','wp-slimstat') ?></td>
	</tr>
	<tr>
		<th scope="row" style="padding-top: 20px"><?php _e('Delete pageviews where','wp-slimstat') ?></th>
		<td>
			<form action="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>" method="post">
				<?php wp_nonce_field( 'maintenance_wp_slimstat', 'maintenance_wp_slimstat_nonce', true, true ); ?>
				<input type="hidden" name="action" value="delete-records" />
				
				<select name="f" id="slimstat-filter-name">
					<?php 
						foreach (wp_slimstat_db::$columns_names as $a_filter_id => $a_filter_info){
							echo "<option value='$a_filter_id'>{$a_filter_info[0]}</option>";
						}
					?>
				</select> 
				<select name="o" id="slimstat-filter-operator">
					<option value="equals"><?php _e('equals','wp-slimstat') ?></option>
					<option value="is_not_equal_to"><?php _e('is not equal to','wp-slimstat') ?></option>
					<option value="contains"><?php _e('contains','wp-slimstat') ?></option>
					<option value="includes_in_set"><?php _e('is included in','wp-slimstat') ?></option>
					<option value="does_not_contain"><?php _e('does not contain','wp-slimstat') ?></option>
					<option value="starts_with"><?php _e('starts with','wp-slimstat') ?></option>
					<option value="ends_with"><?php _e('ends with','wp-slimstat') ?></option>
					<option value="sounds_like"><?php _e('sounds like','wp-slimstat') ?></option>
					<option value="is_greater_than"><?php _e('is greater than','wp-slimstat') ?></option>
					<option value="is_less_than"><?php _e('is less than','wp-slimstat') ?></option>
					<option value="matches"><?php _e('matches','wp-slimstat') ?></option>
					<option value="does_not_match"><?php _e('does not match','wp-slimstat') ?></option>
					<option value="is_empty"><?php _e('is empty','wp-slimstat') ?></option>
					<option value="is_not_empty"><?php _e('is not empty','wp-slimstat') ?></option>
				</select>
				<input type="text" name="v" id="slimstat-filter-value" value="" size="20">
				<input type="submit" value="<?php _e('Apply','wp-slimstat') ?>" class="button-secondary" name="Submit"
					onclick="return(confirm('<?php _e('Are you sure you want to PERMANENTLY delete these records from your database?','wp-slimstat'); ?>'))" />
			</form>
		</td>
	</tr>
	<tr class="alternate">
		<th scope="row">
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=truncate-table"
				onclick="return(confirm('<?php _e('Are you sure you want to PERMANENTLY DELETE ALL the records from your database?','wp-slimstat'); ?>'))"><?php _e('Delete All Records','wp-slimstat'); ?></a>
		</th>
		<td>
			<span class="description"><?php _e('Erase all the information collected so far by Slimstat, including the archive. This operation <strong>does not</strong> reset your settings.','wp-slimstat') ?></span>
		</td>
	</tr>
	<tr >
		<th scope="row">
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=truncate-archive"
				onclick="return(confirm('<?php _e('Are you sure you want to PERMANENTLY DELETE ALL the records from your archive?','wp-slimstat'); ?>'))"><?php _e('Delete Archive','wp-slimstat'); ?></a>
		</th>
		<td>
			<span class="description"><?php _e("Erase all the archived records. This operation cannot be undone.",'wp-slimstat') ?></span>
		</td>
	</tr>
	<tr  class="alternate">
		<?php if (empty($check_index)): ?>
		<th scope="row">
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=activate-indexes"><?php _e("Improve Performance",'wp-slimstat'); ?></a>
		</th>
		<td>
			
			<span class="description"><?php _e("Please note that you will need about 30% more DB space to store the extra information required.",'wp-slimstat') ?></span>
		</td>
		<?php else: ?>
		<th scope="row">
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=deactivate-indexes"><?php _e('Save DB Space','wp-slimstat'); ?></a>
		</th>
		<td>
			<span class="description"><?php _e("Please note that by removing table indexes, Slimstat's performance will be affected.",'wp-slimstat') ?></span>
		</td>
		<?php endif ?>
	</tr>
	<tr>
		<th scope="row">
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=import-data-from-old-tables"
				onclick="return(confirm('<?php _e('Hold on tight, we are about to import all your old data. Are you sure you want to proceed?','wp-slimstat'); ?>'))"><?php _e('Import old data','wp-slimstat'); ?></a>
		</th>
		<td>
			<span class="description"><?php _e("Import all the records from the old table structure. No data will be deleted from your database.",'wp-slimstat') ?></span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('MaxMind IP to Country','wp-slimstat') ?></td>
	</tr>
	<tr>
		<th scope="row">
			<?php if (!file_exists(wp_slimstat::$maxmind_path)): ?>
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=download-maxmind"
				onclick="return(confirm('<?php _e('Do you want to download and install the geolocation database from MaxMind\'s server?','wp-slimstat'); ?>'))"><?php _e("Install GeoLite DB",'wp-slimstat'); ?></a>
			<?php else: ?>
			<a class="button-secondary" href="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>&amp;action=delete-maxmind"
				onclick="return(confirm('<?php _e('Do you want to uninstall the geolocation database?','wp-slimstat'); ?>'))"><?php _e("Uninstall GeoLite DB",'wp-slimstat'); ?></a>
			<?php endif; ?>
		</th>
		<td>
			<span class="description"><?php _e("The <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>MaxMind GeoLite library</a> used to geolocate visitors is released under the Creative Commons BY-SA 3.0 license, and cannot be directly bundled with the plugin because of license incompatibility issues. We are mandated to have the user take an affirmative action in order to enable this functionality.",'wp-slimstat') ?></span>
		</td>
			
	</tr>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('Import and Export','wp-slimstat') ?></td>
	</tr>
	<tr>
		<td colspan="2">
			<strong><?php _e("Here below you can find the current configuration string for Slimstat. You can update your settings by pasting a new string inside the text area and clicking the Import button.",'wp-slimstat') ?></strong>
			<form action="<?php echo wp_slimstat_admin::$config_url.$current_tab ?>" method="post">
				<?php wp_nonce_field( 'maintenance_wp_slimstat', 'maintenance_wp_slimstat_nonce', true, true ) ?>
				<input type="hidden" name="action" value="import-settings" />
				<textarea name="import-slimstat-settings" style="width:100%" rows="5" onClick="this.select();"><?php echo serialize(wp_slimstat::$options) ?></textarea><br/>
				<input type="submit" value="<?php _e('Import','wp-slimstat') ?>" class="button-secondary"
					onclick="return(confirm('<?php _e('Are you sure you want to OVERWRITE your current settings?','wp-slimstat'); ?>'))">
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="slimstat-options-section-header"><?php _e('Database Information','wp-slimstat') ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Engine','wp-slimstat') ?></th>
		<td><?php 
			echo $details_wp_slim_tables[0]['Engine']; 
			if (!empty($have_innodb) && $have_innodb[0]['Value'] == 'YES' && $details_wp_slim_tables[0]['Engine'] == 'MyISAM'){
				echo ' [<a href="'.wp_slimstat_admin::$config_url.$current_tab.'&amp;action=switch-engine">'.__('switch to InnoDB','wp-slimstat').'</a>]';
			}
		?></td>
	</tr>
	<?php
		foreach ($details_wp_slim_tables as $i => $a_table){
			$base = ($a_table['Data_length'] != 0)?(log($a_table['Data_length']) / log(1024)):0;
			$a_table['Data_length_with_suffix'] = round(pow(1024, $base - floor($base)), 2).' '.$suffixes[floor($base)];
			
			echo '<tr '.(($i%2==0)?'class="alternate"':'').">
					<th scope='row'>{$a_table['Name']}</th>
					<td>".$a_table['Data_length_with_suffix'].' ('.number_format($a_table['Rows'], 0).' '.__('records','wp-slimstat').')</td>
				  </tr>';
		}
	?>
</tbody>
</table>