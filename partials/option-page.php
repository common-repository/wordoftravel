<style>
	h2.title{
		border-top: 1px solid #d1d1d1;
		padding-top: 16px;
		margin: 5px 0;
	}
.wotb_error{
		background: #b61919;
		color: #fff;
		padding: 10px;
}
.wotb_success{
		background: #23b619;
		color: #fff;
		padding: 10px;
}
</style>
<div class="wrap">
	<h1><?php echo __('Wordoftravel',$this->plugin_name); ?></h1>
	<form method="post" action="">
		<?php
			$this->show_unique_code_message();
		?>
		<h2 class="title"><?php echo __('Confirm ownership of your blog',$this->plugin_name); ?></h2>
		<i>This is only required during initial blog registration</i><br/>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="wotb_unique_code"><?php echo __('Unique Code',$this->plugin_name); ?></label>
					</th>
					<td>
						<input required name="wotb_unique_code" type="text" id="wotb_unique_code" value="<?php echo esc_attr( get_option('wotb_unique_code') ); ?>" class="regular-text">
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Validate"></p>
	</form>


	<form method="post" action="options.php">
		<?php
			settings_fields( 'wotb_link_settings' );
			do_settings_sections( 'wotb_link_settings' );
		?>
		<h2 class="title"><?php echo __('Help Promote wordoftravel',$this->plugin_name); ?></h2>
		<p><?php echo __('Help us spread the word about wordoftravel by linking back to us from your blog home or posts. Select from the follow links and art to display.',$this->plugin_name); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="wotb_type_of_link"><?php echo __('Type of link',$this->plugin_name); ?></label>
					</th>
					<td>
						<select name="wotb_type_of_link" class="regular-text" id="wotb_type_of_link">
							<?php
								$links = array(
									'Link to us',
									'Image link to us',
								);
								$link_i=0;
								foreach($links as $link){ $link_i++;
							?>
							<option <?php if(intval(get_option('wotb_type_of_link')) === $link_i){echo 'selected';} ?> value="<?php echo $link_i; ?>"><?php echo $link; ?></option>
							<?php } ?>
						</select>
						<span <?php if(intval(get_option('wotb_type_of_link')) !== 1){echo 'style="display: none";';} ?> id="preview_link1">
							<a class="wotb_link_to_us" href="https://wordoftravel.com"><?php echo __('Find more travel blogs like this on wordoftravel.com',$this->plugin_name)?></a>
						</span>
						<span <?php if(intval(get_option('wotb_type_of_link')) !== 2){echo 'style="display: none";';} ?> id="preview_link2">
							<a class="wotb_link_to_us" href="https://wordoftravel.com"><img src="https://wordoftravel.com/images/bloggerlinksmall.png" alt="<?php echo __('Find more travel blogs like this on wordoftravel.com',$this->plugin_name) ?>"></a>
						</span>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="wotb_html_place"><?php echo __('Place to insert the Link',$this->plugin_name); ?></label>
					</th>
					<td>
						<select name="wotb_html_place" class="regular-text" id="wotb_html_place">
							<option value=""><?php echo __('Choose One',$this->plugin_name); ?></option>
							<?php
								$html_places = array(
									'Homepage Footer',
									'Homepage Sidebar',
									'Post Footer',
									'Post Sidebar',
								);
								$place_i = 0;
								foreach($html_places as $html_place){ $place_i++;
							?>
							
							<option <?php if(intval(get_option('wotb_html_place')) === $place_i){echo 'selected';} ?> value="<?php echo $place_i; ?>"><?php echo $html_place; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				

				<tr id="wotb_link_style_div"
				 <?php if(intval(get_option('wotb_type_of_link')) !== 1 || intval(get_option('wotb_html_place')) === 2 || intval(get_option('wotb_html_place')) === 4){ echo 'style="display: none;"'; } ?>

				>
					<th scope="row">
						<label for="wotb_link_style"><?php echo __('Style of the link',$this->plugin_name); ?></label>
					</th>
					<td>
						<select name="wotb_link_style" class="regular-text" id="wotb_link_style">
							<?php
								$link_styles = array(
									'Plain Link',
									'Dark Link',
								);
								$place_i = 0;
								foreach($link_styles as $link_style){ $place_i++;
							?>
							
							<option <?php if(intval(get_option('wotb_link_style')) === $place_i){echo 'selected';} ?> value="<?php echo $place_i; ?>"><?php echo $link_style; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>

			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>
</div>