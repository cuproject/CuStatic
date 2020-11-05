<?php if (empty($cuStaticConfigs)): ?>
	<section class="bca-actions">
		利用する前に [ <?php echo $this->BcBaser->link('オプション設定', ['action' => 'config']) ?> ] を行ってください。
	</section>
<?php else: ?>

<section class="bca-section" data-bca-section-type='form-group'>

	<?php echo $this->BcForm->create('CuStatic', ['type' => 'file']) ?>

	<?php echo $this->BcFormTable->dispatchBefore() ?>

	<div id="status">
		<progress id="status_progres" max="20" value="0"></progress>
		<div id="status_message"></div>
	</div>

	<?php echo $this->BcForm->hidden('mode', ['value' => 'export']) ?>

	<?php echo $this->BcForm->dispatchAfterForm('option') ?>

	<?php echo $this->BcFormTable->dispatchAfter() ?>

	<section class="bca-actions">
		<div class="bca-actions__main">
			<?php
				echo $this->BcForm->submit(
					__d('baser', '静的HTML出力'),
					[
						'id' => 'BtnSave',
						'div' => false,
						'class' => 'button bca-btn bca-actions__item',
						'data-bca-btn-type' => 'save',
						'data-bca-btn-size' => 'lg',
						'data-bca-btn-width' => 'lg',
					]
				);
			?>
		</div>
	</section>

	<div class="bca-collapse__action">
		<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse" data-bca-target="#console_wrapper" aria-expanded="false" aria-controls="console_wrapper">
			最新ログ表示 <i class="bca-icon--desc bca-collapse__btn-icon"></i>
		</button>
	</div>
	<div id="console_wrapper" class="bca-collapse" data-bca-state="">
		<pre id="console"></pre>
		<?php echo $this->BcBaser->link('ログファイルをダウンロード', ['action' => 'log_download'], ['class' => 'bca-btn']) ?>
	</div>

	<?php echo $this->BcForm->end() ?>

</section>

<script>
$(function(){
	run();
	get_status();

	var timer = null;
	if (timer) {
		clearInterval(timer);
	}
	timer = setInterval(run, 2000);

	function run(){
		$('#console').load('<?php echo Router::url([
			'admin' => true,
			'plugin' => 'cu_static',
			'controller' => 'cu_statics',
			'action' => 'tail',
		]); ?>');
		$('#console').animate({scrollTop: $('#console')[0].scrollHeight}, 'fast');
	}

	var status_timer = null;
	if (status_timer) {
		clearInterval(status_timer);
	}
	status_timer = setInterval(get_status, 2000);
	function get_status() {
		$.ajax({
			type: 'GET',
			url: '<?php echo Router::url([
				'admin' => true,
				'plugin' => 'cu_static',
				'controller' => 'cu_statics',
				'action' => 'get_status',
			]); ?>',
			cache: false
		}).done(function( result ) {
			var data = $.parseJSON(result);
			data.status = Number(data.status);
			data.progress = Number(data.progress);
			data.progress_max = Number(data.progress_max);
			if (data.status == 1) {
				$('#status').show();
			}

			$('#status_progres').val(data.progress);
			$('#status_progres').attr('max', data.progress_max);
			if (data.progress >= data.progress_max) {
				$('#status_message').html('完了');
			} else if (data.progress <= '0') {
				$('#status_message').html('');
			} else {
				$('#status_message').html('処理中 (' + parseInt((data.progress / data.progress_max) * 100, 10)  + ' %)');
			}
		});
	}
});
</script>

<style>
#console {
	width: 100%;
	height: 400px;
	overflow: auto;
	border: 1px solid #999999;
	font-size: 12px;
	font-family: consolas;
}
progress {
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	border: none;
	background-color: #eeeeee;
	color: #6fa83d;
}
progress::-webkit-progress-value {
	background-color: #6fa83d;
}
progress::-webkit-progress-bar {
	background-color: #eeeeee;
}
progress::-moz-progress-value {
	background-color: #eeeeee;
}
progress::-moz-progress-bar {
	background-color: #6fa83d;
}

#status {
	display: none;
}
#status_progres {
	width: 100%;
	height: 20px;
}
#status_message {
	margin: 0 auto;
	text-align: center;
}
</style>

<?php endif; ?>
