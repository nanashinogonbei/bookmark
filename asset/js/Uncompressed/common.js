const userManager = new Vue({

	data: {
		user_id: undefined,
		user_token: undefined
	},

	methods: {

		/**
		 * ユーザー情報をプロパティに保存
		 *
		 * @param integer id
		 * @param string token
		 * @return boolean
		**/
		saveUser: function ( id, token ) {
			this.user_id = id;
			this.user_token = token;
		},

		/**
		 * localStorageの存在確認
		 *
		 * @param none
		 * @return boolean 有: true, 無: false
		**/
		cheakLocStor: function () {
			if ( localStorage.getItem('user_id') && localStorage.getItem('user_token') ) {
				return true;
			}
			else {
				return false;
			}
		}

	}

});



const addBookmark = new Vue({
	el: '#addBookmark',
	data: {
		show: true
	},
	methods: {

		/**
		 * 入力されたURLのtitleを取得
		 * 取得したtitleを出力
		 *
		 * @param none
		 * @return none
		**/
		get_outpage_title: function () {

			const form = document.forms.addBookmark;
			const params_array = {
				url: form.url.value
			}
			const params = axiosParamsGenerat( params_array );

			form.title.value = '';

			if ( params_array.url ) {

				form.title.classList.add('js-txt-loader');
				form.title.classList.remove('form-txt--err');

				axios({
					method	: 'POST',
					url		: './async/get_outpage_title.php',
					timeout	: 3000,
					data		: params
				})
				.then( (response) => {
					// titleを出力
					form.title.classList.remove('js-txt-loader');
					form.title.value = response.data;
					// ボタンの有効化
					form.btn_exe.classList.remove('agree-btn--disabled');
					form.btn_exe.classList.add('agree-btn--active');
					form.btn_exe.disabled = false;
				})
				.catch( (error) => {
					// title
					form.title.value = "タイトルが取得できませんでした。";
					form.title.classList.remove('js-txt-loader');
					form.title.classList.add('form-txt--err');
					// btn
					form.btn_exe.classList.add('agree-btn--disabled');
					form.btn_exe.disabled = false;
				});

			}

		},

		/**
		 * 「ブックマークを追加する」ボタンをクリック
		 * userの存在チェック
		 * @see なければ生成して登録：userManager::createUser()
		 * @see あれば登録			：bookmarkManager::add_bookmark()
		 *
		 * @param none
		 * @return none
		**/
		add_exe: function () {
			if ( userManager.cheakLocStor() ) {
				this.add_bookmark();
			}
			else {
				console.log('ユーザー情報がありません。');
			}
		},

		/**
		 * 入力内容をDBに保存
		 *
		 * @param none
		 * @return none
		 * @see bookmarkManager::get_bookmark()
		**/
		add_bookmark: function(){
			const form = document.forms.addBookmark;
			const params_array = {
				user_id	: userManager.user_id,
				url			: form.url.value,
				title		: form.title.value,
				view		: form.view.value
			}
			const params = axiosParamsGenerat( params_array );

			axios({
				method	: 'POST',
				url			: './async/add_bookmark.php',
				timeout	: 3000,
				data		: params
			})
			.then( (response) => {
				console.log(response.data);
				if( response.data ){
					this.show = false;
					setTimeout( () => { this.show = true; }, 3000);
					// ボタンの無効化
					form.btn_exe.classList.remove('agree-btn--active');
					form.btn_exe.classList.add('agree-btn--disabled');
					form.btn_exe.disabled = true;
					// 追加内容をリストに表示
					bookmarkManager.get_bookmark();
				}
			})
			.catch( (error) => {
				console.log(error);
			});
		}

	}
});



const addCategory = new Vue({
	el: '#addCategory',
	data: {
		show: true
	},
	methods: {

		/**
		 * 「カテゴリーを追加する」ボタンの有効化/無効
		 *
		 * @param none
		 * @return none
		**/
		input_moniter: function (e) {
			let target = e.srcElement;
			const form = document.forms.addCategory;
			if ( target.value.length > 0 ) {
				form.btn_exe.classList.remove('agree-btn--disabled');
				form.btn_exe.classList.add('agree-btn--active');
				form.btn_exe.disabled = false;
			} else {
				form.btn_exe.classList.remove('agree-btn--active');
				form.btn_exe.classList.add('agree-btn--disabled');
				form.btn_exe.disabled = true;
			}
		},

		/**
		 * カテゴリを追加
		 *
		 * @param none
		 * @return none
		 * @see bookmarkManager::get_category()
		**/
		add_exe: function() {

			if ( ! userManager.cheakLocStor() ) {
				console.log('idないっす');
				return;
			}

			const form = document.forms.addCategory;
			const params_array = {
				user_id	: userManager.user_id,
				category	: form.category.value
			}
			const params = axiosParamsGenerat( params_array );

			axios({
				method	: 'POST',
				url		: './async/add_category.php',
				timeout	: 3000,
				data		: params
			})
			.then( (response) => {
				if( response.data ){
					this.show = false;
					setTimeout( () => { this.show = true; }, 3000);
					// ボタンの無効化
					form.btn_exe.classList.remove('agree-btn--active');
					form.btn_exe.classList.add('agree-btn--disabled');
					form.btn_exe.disabled = true;
					form.category.value = '';
				}
				// カテゴリ情報の再取得
				bookmarkManager.get_category();
			})
			.catch( (error) => {
				console.log(error);
			});
		}
	}
});



const bookmarkManager = new Vue({
	el: '#bookmarkList',
	data: {
		view				: true,
		btn_label		: 'ブックマークを編集する',
		categorys		: [],
		categorys_id: [],
		bookmarks		: []
	},
	/*
	computed: {
		orderBookmarks: function () {
			if ( this.bookmarks.length !== 0 ) {
				return _.orderBy(this.bookmarks, 'order');
			}
		}
	},
	*/
	methods: {

		/**
		 * DBからカテゴリ情報を取得
		 *
		 * @param none
		 * @return none
		**/
		get_category: function( callback ) {
			const params_array = {
				user_id	: userManager.user_id
			}
			const params = axiosParamsGenerat( params_array );

			axios({
				method	: 'POST',
				url			: './async/get_category.php',
				timeout	: 3000,
				data		: params
			})
			.then( (response) => {
				if( response.data ){
					this.categorys = response.data;
					this.categorys.unshift({'category_id':0, 'label':'未分類'});
					if ( callback ) { callback(); }
				}
			})
			.catch( (error) => {
				console.log('get_category: ' + error);
			});
		},

		/**
		 * this.categorysからcategory_idだけをthis.categorys_id[]に格納
		 *
		 * @param none
		 * @return none
		**/
		set_categorys_id: function() {
			for ( let i = 0; i < this.categorys.length; i++ ) {
				this.categorys_id[i] = this.categorys[i].category_id;
			}
		},

		/**
		 * DBからブックマーク情報を取得
		 *
		 * @param none
		 * @return none
		 * @see bookmarkManager::print_bookmark()
		**/
		get_bookmark: function() {
			this.set_categorys_id();

			const form = document.forms.addBookmark;
			const params_array = {
				user_id			: userManager.user_id,
				category_id	: this.categorys_id
			}
			const params = axiosParamsGenerat( params_array );

			axios({
				method	: 'POST',
				url		: './async/get_bookmark.php',
				timeout	: 3000,
				data		: params
			})
			.then( (response) => {
				this.print_bookmark( response.data );
			})
			.catch( (error) => {
				console.log(error);
			});
		},

		/**
		 * ブックマーク情報を表示
		 *
		 * @param object(json) rData
		 * @return none
		**/
		print_bookmark: function( rData ) {
			this.bookmarks = rData;
			/*
			if ( this.bookmarks.length === 0 ) {
				this.bookmarks = rData;
			}
			else {
				this.bookmarks.push( rData.pop() );
			}
			*/
		},

		/**
		 * ブックマークの表示/編集の切り替え
		 *
		 * @param none
		 * @return none
		**/
		edit_and_save: function() {
			if ( this.view === true ) {
				this.view = false;
				this.btn_label = '編集を保存する';
			}
			else {
				this.view = true;
				this.btn_label = 'ブックマークを編集する';
				bookmarkManager.save_bookmark();
			}
		},

		/**
		 * 編集したブックマークをDBに保存
		 *
		 * @param none
		 * @return none
		**/
		save_bookmark: function () {
			const form = document.forms.bookmark;
			let line = form.querySelectorAll('li');
			let input = [];
			let temp = [];
			let db = [];

			for ( let i = 0; i < line.length; i++ ) {
				input = line[i].querySelectorAll('.lists input');

				for ( let j = 0; j < input.length; j++ ) {
					//temp[input[j].name] = input[j].value;
					temp[j] = input[j].value;
				}

				db[i] = temp;
				temp = [];
			}

			const params = axiosParamsGenerat( db );

			axios({
				method	: 'POST',
				url			: './async/update_bookmark.php',
				timeout	: 3000,
				data		: params
			})
			.then( (response) => {
				console.log(response.data);
			})
			.catch( (error) => {
				console.log(error);
			});

		},

		/**
		 * DnD後のイベント
		 *
		 * @param object e:Event
		 * @return none
		**/
		onUPDATE: function (e) {
			console.log('update:');
			console.log(e);
			orderChangeSort( e.srcElement );
		},
		onADD: function (e) {
			orderChangeSort( e.target );
		},
		onREMOVE: function (e) {
			orderChangeSort( e.from );
		},

		/**
		 * 削除イベント
		 *
		 * @param object e:Event
		 * @return none
		**/
		element_delete: function (e) {
			let target = e.target;
			let rm_obj = target.closest('li');
			rm_obj.remove();
			orderChangeSort( e.path[3] );
		}

	}
});




/**
 * category_id と order を一括変更
 *
 * @param array params_array
 * @return object params
**/
const orderChangeSort = ( target ) => {
	let category = target.dataset.category;
	let count = target.childElementCount;
	for (let i = 0; i < count; i++) {
		target.childNodes[i].children[2].children[1].value = category;
		target.childNodes[i].children[2].children[2].value = i;
	}
}


/**
 * axiosに受け渡すパラメータを生成する
 *
 * @param array params_array
 * @return object params
**/
const axiosParamsGenerat = ( params_array ) => {

	let params = new URLSearchParams();

	for( let key in params_array ) {
		params.append( key, params_array[key] );
	}

	return params;

}


/**
 * オブジェクトのソート
 *
 * @param object data
 * @param string key
 * @param string order ( DESC, ASC )
 * @return object data
**/
const objectArraySort = ( data, key, order = 'DESC' ) => {

	// orderの基本は降順(DESC)
	let num_a = -1;
	let num_b = 1;

	// orderの指定があったら昇順(ASC)
	if(order === 'ASC'){
	  num_a = 1;
	  num_b = -1;
	}

	data = data.sort(function(a, b){
		let x = a[key];
		let y = b[key];
		if (x > y) return num_a;
		if (x < y) return num_b;
		return 0;
	});

	return data;

}



const orderBookmarks = ( array ) => {
	if ( array.length !== 0 ) {
		return _.orderBy(array, 'order');
	}
}


window.addEventListener('load', function(){

	if ( userManager.cheakLocStor() === true ) {
		userManager.saveUser( localStorage.getItem('user_id'), localStorage.getItem('user_token') );
	}

	bookmarkManager.get_category( () => {
		bookmarkManager.get_bookmark();
	});

});
