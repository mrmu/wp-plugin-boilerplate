<?php
abstract class Plugin_Slug_Settings_Abstract{

	abstract protected function get_setting();

	// get data from DB
	public function get_fd_option( $fd_id = '' ) {
		$obj_setting = $this->get_setting();
		$opts = get_option( $obj_setting['option_key'] );
		if (!empty($fd_id)) {
			if (!empty($opts[$fd_id])) {
				return $opts[$fd_id];
			}else{
				return '';
			}
		}
		return $opts;
	}

	private function get_section_fd_data( $fd_id, $sec_id ) {
		$obj_setting = $this->get_setting();
		$sections = $obj_setting['sections'];
		foreach ($sections as $sec) {
			if ($sec['id'] === $sec_id) {
				return $sec['fields'][$fd_id];
			}
		}
		return false;
	}

	public function get_fd_setting( $arg ) {
		$fd_id = $arg['fd_id'];
		$sec_id = $arg['sec_id'];
		$obj_setting = $this->get_setting();
		$fd_data = $this->get_section_fd_data( $fd_id, $sec_id );

		// set/get with custom option key
		if (!empty($fd_data['option_key'])) {
			$option_key = $fd_data['option_key']; 
			$fd_name = $option_key;
		}else{
			$option_key = $obj_setting['option_key'];
			$fd_name = $option_key.'['.$fd_id.']';
		}

		$fd_val = '';
		if (!empty($fd_data['val'])) {
			$fd_val = $fd_data['val'];
		}else{
			$fd_val = $this->get_fd_option( $fd_id );
		}

		$fd = array( 
			'fd_id'			=> $fd_id,
			'name' 			=> $fd_name,
			'placeholder' 	=> (isset($fd_data['placeholder']))?$fd_data['placeholder']:'',
            'desc' 			=> (isset($fd_data['desc']))?$fd_data['desc']:'',
            'options' 		=> (isset($fd_data['options']))?$fd_data['options']:'',
			'type' 			=> (isset($fd_data['type']))?$fd_data['type']:'',
			'class' 		=> (isset($fd_data['class']))?$fd_data['class']:'',
			'id' 			=> (isset($fd_data['id']))?$fd_data['id']:'',
			'title' 		=> (isset($fd_data['title']))?$fd_data['title']:'',
			'accept' 		=> (isset($fd_data['accept']))?$fd_data['accept']:'',
			'show_files' 	=> (isset($fd_data['show_files']))?$fd_data['show_files']:'',
			'val' 			=> $fd_val
		);
		return $fd;
	}

	// Hidden
	public function display_hidden_fd($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_id = $fd['id'];
		$fd_val = $fd['val'];
		?>
		<input type="hidden" class="hide-tr" name="<?php echo $fd_name;?>" value="<?php echo $fd_val; ?>" />
		<?php
	}

	// 上傳檔案
	public function display_upload($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_id = $fd['id'];
		$fd_fdid = $fd['fd_id'];
		$fd_class = $fd['class'];
		$fd_title = $fd['title'];
		$fd_accept = $fd['accept'];
		$fd_show_files = $fd['show_files'];
		?>
		<label for="<?php echo $fd_name;?>" class="upload"> <?php echo $fd_title; ?> </label>
		<div class="custom-file">
			<input type='file' id="<?php echo $fd_name;?>" name='<?php echo $fd_name;?>' accept='<?php echo $fd_accept;?>' class="custom-file-input">
			<label class="custom-file-label" for="<?php echo $fd_name;?>">Choose file</label>
		</div>
		<?php
		if ($fd_show_files === 'yes') {
			?>
			<div>
				<?php
				// echo 'fdid:'.$fd_fdid;
				// 匯入檔列表
				$imp_args = array(
					'numberposts'      => 1,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'meta_key'         => '_attachment_fd_id',
					'meta_value'       => $fd_fdid,
					'post_type'        => 'attachment'
				);
				$imp_atts = get_posts($imp_args);
				if (!empty($imp_atts)) {
					?>
					<table class="widefat fixed settings" cellspacing="0" style="width: auto;">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ($imp_atts as $att) {
						// 在 form 內不能再插入 form
						echo '<tr class="">';
						echo '<td>'.$att->ID.'</td><td>'.$att->post_title.'</td><td>'.$att->post_date.'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
					</table>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
	
	// 按鈕
	public function display_btn($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_id = $fd['id'];
		$fd_val = $fd['val'];
		$fd_class = $fd['class'];
		$fd_title = $fd['title'];
		$fd_type = $fd['type'];
		?>
		<button 
			type="<?php echo $fd_type;?>" 
			id="<?php echo $fd_id;?>" 
			name="<?php echo $fd_name;?>" 
			class="<?php echo $fd_class; ?>" 
			value="<?php echo $fd_val;?>"
		>
			<?php echo $fd_title; ?>
		</button>
		<?php
	}
	
	// 日期
    public function display_date_fd($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_placeholder = $fd['placeholder'];
		$fd_desc = $fd['desc'];
		$fd_val = $fd['val'];
		?>
		<input type="date" size="80" name="<?php echo $fd_name;?>" value="<?php echo $fd_val; ?>" placeholder="<?php echo $fd_placeholder;?>" />
		<p class="description" ><?php echo $fd_desc;?></p>
		<?php
    }

	// 文字
	public function display_text_fd($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_placeholder = $fd['placeholder'];
		$fd_desc = $fd['desc'];
		$fd_val = $fd['val'];
		?>
		<input type="text" size="80" name="<?php echo $fd_name;?>" value="<?php echo $fd_val; ?>" placeholder="<?php echo $fd_placeholder;?>" />
		<p class="description" ><?php echo $fd_desc;?></p>
		<?php
	}

	// Checkbox
	public function display_checkbox_fd($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
		$fd_desc = $fd['desc'];
		$fd_val = $fd['val'];		
		?>
		<input type="checkbox" name="<?php echo $fd_name;?>" value="1" <?php checked(1, $fd_val, true); ?>>
		<p class="description" ><?php echo $fd_desc;?></p>
		<?php
	}

	// 選擇框
	public function display_select_fd($arg) {
		$fd = $this->get_fd_setting($arg);
		$fd_name = $fd['name'];
        $fd_desc = $fd['desc'];
        $fd_options = $fd['options'];
		$fd_val = $fd['val'];
		?>
		<select name="<?php echo $fd_name;?>">
			<?php
			foreach ($fd_options as $key => $val) {
				?>
				<option value="<?php echo $val?>" <?php selected($val, $fd_val);?>><?php echo $key?></option>
				<?php
			}
			?>
		</select>
		<p class="description" ><?php echo $fd_desc;?></p>
		<?php
    }

}