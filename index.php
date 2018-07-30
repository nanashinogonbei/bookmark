<!DOCTYPE html>
<html dir="ltr" lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no,address=no,email=no">
	<title>ただのbookmark</title>
	<link rel="stylesheet" type="text/css" href="./asset/css/main.css" media="screen">
	<style>
		#area {
			width: 500px;
			height: 500px;
			background-color: #ddd;
		}
		.sortable-chosen {
			color: #fff;
			background-color: #edb54c;
		}
	</style>
</head>

<body>

	<p><a href="./account_check.php">アカウントを発行する</a></p>

	<div id="warp">

		<div id="addBookmark">
		<form name="addBookmark">
				<p>url:
					<input type="text" name="url" @blur="get_outpage_title">
				</p>
				<p>タイトル:
					<input type="text" name="title">
				</p>
				<p>
					他ユーザーへの表示：
					<select name="view">
						<option value="1">表示</option>
						<option value="0">非表示</option>
					</select>
				</p>
				<transition name="fade">
					<p class="txt-comprete" v-if="!show">ブックマークを追加しました。</p>
				</transition>
				<button type="button" name="btn_exe" @click="add_exe" class="agree-btn--disabled" disabled>ブックマークを追加する</button>
		</form>
		</div>

		<div id="addCategory">
		<form name="addCategory">
			<div>
				カテゴリーを追加：
				<input type="text" name="category" @keyup="input_moniter">
				<button type="button" name="btn_exe" @click="add_exe" class="agree-btn--disabled" disabled>カテゴリーを追加する</button>
			</div>
			<transition name="fade">
				<p class="txt-comprete" v-if="!show">カテゴリーを追加しました。</p>
			</transition>
		</form>
		</div>

		<div id="bookmarkList">
			<div><button type="button" @click="edit_and_save" class="agree-btn--active">{{btn_label}}</button></div>

			<form name="bookmark">
			<div v-for="(category, idx) in categorys">
				<p class="category_name">{{ category.label }}</p>

				<ul class="lists" v-if="view" v-model="bookmarks[idx]" :data-category="category.category_id" :options="{group:'ITEMS'}">
					<li v-for="bookmark in bookmarks[idx]">
						<div class="nomal">
							<!--span>{{ bookmark.id }} : </span-->
							<a :href="bookmark.url" target="_blank">{{ bookmark.title }}</a>
						</div>
					</li>
				</ul>

				<draggable element="ul" class="lists" v-if="!view" v-model="bookmarks[idx]" :data-category="category.category_id" :options="{group:'ITEMS', handle: '.handler'}" @update="onUPDATE" @add="onADD" @remove="onREMOVE">
					<li v-for="bookmark in bookmarks[idx]">
						<div class="handler">■</div>
						<div class="edit">
							title:<input type="text" name="title" :value="bookmark.title">
							url:<input type="text" name="url" :value="bookmark.url">
							view:<input type="text" name="view" :value="bookmark.view">
						</div>
						<div class="other">
							user:<input type="text" name="user_id" :value="bookmark.user_id">
							category:<input type="text" name="category_id" :value="bookmark.category_id">
							order:<input type="text" name="order" :value="bookmark.order">
							id:<input type="text" name="id" :value="bookmark.id">
						</div>
						<div class="trash">
							<img src="./asset/image/common/trash.svg" @click="element_delete">
						</div>
					</li>
				</draggable>

			</div>
			</form>
		</div>

	<script type="text/javascript" src="./asset/js/vue.min.js"></script>
	<script type="text/javascript" src="./asset/js/Sortable.min.js"></script>
	<script type="text/javascript" src="./asset/js/vuedraggable.min.js"></script>
	<script type="text/javascript" src="./asset/js/axios.min.js"></script>
	<script type="text/javascript" src="./asset/js/lodash.min.js"></script>
	<script type="text/javascript" src="./asset/js/common.min.js"></script>

</body>

</html>