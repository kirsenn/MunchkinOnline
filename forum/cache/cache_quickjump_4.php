<?php

if (!defined('FORUM')) exit;
define('FORUM_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?><form id="qjump" method="get" accept-charset="utf-8" action="http://funcardgame.ru/forum/viewforum.php">
	<div class="frm-fld frm-select">
		<label for="qjump-select"><span><?php echo $lang_common['Jump to'] ?></span></label><br />
		<span class="frm-input"><select id="qjump-select" name="id">
			<optgroup label="Форум">
				<option value="2"<?php echo ($forum_id == 2) ? ' selected="selected"' : '' ?>>Общий</option>
				<option value="6"<?php echo ($forum_id == 6) ? ' selected="selected"' : '' ?>>Новости</option>
				<option value="5"<?php echo ($forum_id == 5) ? ' selected="selected"' : '' ?>>Обсуждение игры</option>
				<option value="4"<?php echo ($forum_id == 4) ? ' selected="selected"' : '' ?>>Вопросы по правилам</option>
				<option value="3"<?php echo ($forum_id == 3) ? ' selected="selected"' : '' ?>>Предложения и пожелания</option>
			</optgroup>
		</select>
		<input type="submit" value="<?php echo $lang_common['Go'] ?>" onclick="return Forum.doQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array);" /></span>
	</div>
</form>
<script type="text/javascript">
		var forum_quickjump_url = "http://funcardgame.ru/forum/viewforum.php?id=$1";
		var sef_friendly_url_array = new Array(5);
	sef_friendly_url_array[2] = "obshchii";
	sef_friendly_url_array[6] = "novosti";
	sef_friendly_url_array[5] = "obsuzhdenie-igry";
	sef_friendly_url_array[4] = "voprosy-po-pravilam";
	sef_friendly_url_array[3] = "predlozheniya-i-pozhelaniya";
</script>
