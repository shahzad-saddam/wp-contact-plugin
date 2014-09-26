<script type="text/javascript">
	function confirm_alert(node) {
	    return confirm("Are you sure you want to delete this entry.");
	}
</script>
<?php 
	global $wpdb;
  	$table_name = $wpdb->prefix . 'ss_earthquake_form';
  	
	if($_GET['action'] == 'delete' && !empty($_GET['entry']) && is_numeric($_GET['entry'])) {
		//die('aya');
		if($wpdb->query("Delete FROM $table_name WHERE id=".trim($_GET['entry']))){
			$deleted = "Entry ID: ". trim($_GET['id']). " is deleted Successfuly";
		}else {

		}	
	}
?>
<div class="wrap">

<?php $results = $wpdb->get_results("SELECT * FROM $table_name", OBJECT ); ?>

<?php if(isset($deleted) && !empty($deleted)): ?>
	<div class="flash info">
		<span class="close"><?=$deleted ?></span>
		
	</div>
<?php endif; ?>

<?php 

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
 }

  Class FT_WP_Table extends WP_List_Table
    {
    	 private $order;
        private $orderby;
        private $posts_per_page = 20;

        public function __construct()
        {
            parent :: __construct(array(
                'singular' => "ftraveler",
                'plural' => "ftraveler",
                'ajax' => true
            ));

            $this->set_order();
            $this->set_orderby();
            $this->prepare_items();
            $this->display();
        }

        private function get_sql_results()
        {
            global $wpdb;
            $args = array('id', 'agent_name','registration_num', 'province', 'city', 'address', 'phone', 'email', 'contact_person', 'competent_person_cell', 'signage',  'signage_reason');
            $sql_select = implode(', ', $args);
            $sql_results = $wpdb->get_results("SELECT " . $sql_select . " FROM " . $wpdb->prefix . "ss_earthquake_form ORDER BY $this->orderby $this->order ");
            foreach($sql_results as &$sql_result){
            	if($sql_result->signage == 0){
            		$sql_result->signage = 'E';
            	} else {
            		$sql_result->signage = 'H';
            	}
            }
            return $sql_results;
        }

        public function set_order()
        {
            $order = 'DESC';
            if (isset($_GET['order']) AND $_GET['order'])
                    $order = $_GET['order'];
            $this->order = esc_sql($order);
        }

        public function set_orderby()
        {
            $orderby = 'id';
            if (isset($_GET['orderby']) AND $_GET['orderby'])
                    $orderby = $_GET['orderby'];
            $this->orderby = esc_sql($orderby);
        }

        /**
         * @see WP_List_Table::ajax_user_can()
         */
        public function ajax_user_can()
        {
            return current_user_can('edit_posts');
        }

        /**
         * @see WP_List_Table::no_items()
         */
        public function no_items()
        {
            _e('No entries found.');
        }

        /**
         * @see WP_List_Table::get_views()
         */
        public function get_views()
        {
            return array();
        }

        /**
         * @see WP_List_Table::get_columns()
         */
        public function get_columns()
        {
            $columns = array(
                'agent_name' => __('Acente Adı'),
                'registration_num' => __('Levha'),
                'province' => __('İl'),
                'city' => __('İlçe'),
                'address' => __('Adres'),
                'phone' => __('Telefon'),
                'email' => __('E-Posta'),
                'contact_person' => __('Yetkili Kişi'),
                'competent_person_cell' => __('Yetkilinin Cep No'),
                'signage' => __('Tabela'),
                'signage_reason' => __('istememe'),
            );
            return $columns;
        }

        /**
         * @see WP_List_Table::get_sortable_columns()
         */
        public function get_sortable_columns()
        {
            $sortable = array(
                'agent_name' => array('agent_name', true),
                'registration_num' => array('registration_num', true),
                'province' => array('province', true),
                'city' => array('city', true),
            );
            return $sortable;
        }

        /**
         * Prepare data for display
         * @see WP_List_Table::prepare_items()
         */
        public function prepare_items()
        {
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array(
                $columns,
                $hidden,
                $sortable
            );

            // SQL results
            $posts = $this->get_sql_results();
            empty($posts) AND $posts = array();

            # >>>> Pagination
            $per_page = $this->posts_per_page;
            $current_page = $this->get_pagenum();
            $total_items = count($posts);
            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ));
            $last_post = $current_page * $per_page;
            $first_post = $last_post - $per_page + 1;
            $last_post > $total_items AND $last_post = $total_items;

            // Setup the range of keys/indizes that contain 
            // the posts on the currently displayed page(d).
            // Flip keys with values as the range outputs the range in the values.
            $range = array_flip(range($first_post - 1, $last_post - 1, 1));

            // Filter out the posts we're not displaying on the current page.
            $posts_array = array_intersect_key($posts, $range);
            # <<<< Pagination
            // Prepare the data
            $permalink = __('Edit:');
            foreach ($posts_array as $key => $post) {
                $link = get_edit_post_link($post->ID);
                $no_title = __('No title set');
                $title = !$post->post_title ? "<em>{$no_title}</em>" : $post->post_title;
                $posts[$key]->post_title = "<a title='{$permalink} {$title}' href='{$link}'>{$title}</a>";
            }
            $this->items = $posts_array;
        }

        /**
         * A single column
         */
        public function column_default($item, $column_name)
        {
            return $item->$column_name;
        }

        function column_agent_name($item) {
		  $actions = array(
		            'delete'    => sprintf('<a onclick="return confirm_alert(this);" href="?page=%s&action=%s&entry=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id),
		        );

		  return sprintf('%1$s %2$s', $item->agent_name, $this->row_actions($actions) );
		}
        /**
         * Override of table nav to avoid breaking with bulk actions & according nonce field
         */
        public function display_tablenav($which)
        {

            ?>
            <div class="tablenav <?php echo esc_attr($which); ?>">
                <!-- 
                <div class="alignleft actions">
                <?php # $this->bulk_actions( $which );    ?>
                </div>
                -->
                <?php
                $this->extra_tablenav($which);
                $this->pagination($which);

                ?>
                <br class="clear" />
            </div>
            <?php
        }

        /**
         * Disables the views for 'side' context as there's not enough free space in the UI
         * Only displays them on screen/browser refresh. Else we'd have to do this via an AJAX DB update.
         * 
         * @see WP_List_Table::extra_tablenav()
         */
        public function extra_tablenav($which)
        {
            global $wp_meta_boxes;
            $views = $this->get_views();
            if (empty($views)) return;

            $this->views();
        }
    }
?>
<?php
function ft_list()
{
    echo '<div class="wrap"><h2>'. __('Başvurular') .'</h2>';
    echo '<div class="export-link"><a href="'. plugins_url( '/include/excel-export.php', __FILE__ ) . '">Export To Excel</a></div>';
    echo '<div class="import-link"><a href="'.  'admin.php?page=ss-earthquake-contact-form/excel-import.php' . '">Import Excel</a></div>';
    $ftList = new FT_WP_Table();
    echo '</div>';
}
	

ft_list();

?>







<style>
.flash.info {
	display: block;
	padding: 20px;
	height: 19px;
	background: #4A8A32;
	border-radius: 6px;
}
th.manage-column {
	font-size: 96%;
	font-weight: bold;
}
.export-link {
    width: 100px;
    float: left;
}
.import-link {
    float: right;
    width: 100px;
}
</style>












<!-- <div class="export-link"><a href="<?php echo plugins_url( '/excel-export.php', __FILE__ ) ?>">Export To Excel</a></div>
<table class="wp-list-table widefat fixed earthquake">
	<thead>
		<tr>
			<th scope="col" class="manage-column column-cb">
				<a href=""><span>Acente Adı</span><span class="sorting-indicator"></span></a>
			</th>
			<th scope="col" class="manage-column column-cb">Levha Kayıt Numarası</th>
			<th scope="col" class="manage-column column-cb">İl</th>
			<th scope="col" class="manage-column column-cb">İlçe</th>
			<th scope="col" class="manage-column column-cb">Adres</th>
			<th scope="col" class="manage-column column-cb">Telefon</th>
			<th scope="col" class="manage-column column-cb">E-Posta</th>
			<th scope="col" class="manage-column column-cb">Yetkili Kişi</th>
			<th scope="col" class="manage-column column-cb">Yetkili Kişinin Cep Telefonu</th>
			<th scope="col" class="manage-column column-cb">abela istiyor musunuz</th>
			<th scope="col" class="manage-column column-cb">Tabela istememe nedeninizi birkaç cümle ile açıklayınız</th>
			<th scope="col" class="manage-column column-cb">Action</th>
		</tr>
	</thead>

	<tbody>
		<?php if(count($results < 1)):?>
			<?php $calss=0; ?>
			<?php foreach($results as $result): ?>
				<tr class="" id="the-list">
					<th><?=$result->agent_name?></th>
					<th><?=$result->registration_num?></th>
					<th><?=$result->province?></th>
					<th><?=$result->city?></th>
					<th><?=$result->address?></th>
					<th><?=$result->phone?></th>
					<th><?=$result->email?></th>
					<th><?=$result->contact_person?></th>
					<th><?=$result->competent_person_cell?></th>
					<th><?=$result->signage?></th>
					<th><?=$result->signage_reason?></th>
					<th><a  onclick="return confirm_alert(this);" href="admin.php?page=ss-earthquake-contact-form/ss-earthquake-contact-form-admin.php?option=delete&id=<?=$result->id?>" >Delete</a></th>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="7">No media attachments found.</td>
			</tr>
		<?php endif; ?>
	</tbody>

</table> -->
<script type="text/javascript">
/*jQuery(document).ready(function(){

jQuery('table.wp-list-table tbody > tr:odd').addClass('alternate');

});*/
</script>
