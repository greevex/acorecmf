
function textarea_not_tab(el){
	$(el).set('onfocus', "");
	$(el).addEvent('keydown', function(ev){
		if (ev.code == 9){
			if (document.selection){
				var s = document.selection.createRange();
				s.text = "\t";
				s.select();
			} else {
				var scroll = this.scrollTop;
				var start = this.selectionStart;
				var end = this.selectionEnd;
				var text = $(this).get('value');
				$(this).set('value', text.substr(0, start) + "\t" + text.substr(end));
				this['selectionStart'] = this['selectionEnd'] = start + 1;
				this.scrollTop = scroll;
			}
			ev.stopPropagation();
			return false;
		}
	}.bind($(el)));
}

window.addEvent('domready', function(){
	Resize();
	window.addEvent('resize', Resize);

	var accordion = new Accordion('h1.menusection', 'ul.menusection', {
		opacity: true,
		onActive: function(toggler, element){
			toggler.setStyle('color', '#333333');
			toggler.setStyle('background', '#ddd');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('color', '#333');
			toggler.setStyle('background', '#ccc');
		}
	}, $('menu'));
});

function Resize(){
	$('main').setStyles({
	'height': $(window).getCoordinates()['height'] - 120
	});
	$('menu').setStyles({
	'height': $(window).getCoordinates()['height'] - 80
	});
}

MODS = Array();
SUB_MODS = Array();
NOW_MOD = '';

function UpdateSubMods(){
	$('opened').getNext().set('html', '');
	for (var i = 0 ; i < SUB_MODS.length ; i++){
		var li = new Element('li', {});
		var a = new Element('a', {
		'href' : '#',
		'onclick' : 'return false;',
		'html' : SUB_MODS[i].name
		});
		a.addEvent('click', function(){
			this.show();
		}.bind(SUB_MODS[i]));
		li.adopt(a);
		$('opened').getNext().adopt(li);
	}
	$('opened').fireEvent('click');
}

function JsonRequest(el, json){
	if (json.err && json.err == "exit"){
		window.location = ROOT + "/manager/";
		return;
	}
	if (json.err) {
		new Alert("Доступ запрещен!");
		return;
	}
	if (json.res) {
		new Alert(json.res);
	}
	if (json.reload) {
		$(el).getParent("div.content").module.reload();
	}
}

var Module = new Class({

	initialize: function(link, name, module, func, options, spec){
		this.mod_name = module + "|" + func + "|" + name;

		if (MODS[this.mod_name]){
			if (MODS[this.mod_name].spec != spec){
				MODS[this.mod_name].options = options;
				MODS[this.mod_name].spec = spec;
				MODS[this.mod_name].load();
			}
			MODS[this.mod_name].show();
			return;
		}
		MODS[this.mod_name] = this;
		this.spec = spec;

		this.content = new Element('div', {
		'id' : this.mod_name,
		'class' : 'content',
		'html' : 'Загрузка...',
		});
		this.content.module = this;
		this.hide();
		$('main').adopt(this.content);

		this.name = name;
		this.url = ROOT + "/manager/ajax/" + module + "/" + func + "/";
		this.options = options;

		this.load();
		this.show();

		this.type = "";
		if ($(link).getParent().getParent().getAttribute('class') != 'menusection'){
			this.type = "opened";
			SUB_MODS[SUB_MODS.length] = this;
			UpdateSubMods();
		}
	},

	load: function(){
		//new Alert("Загрузка...");
		new Request.JSON({
			url: this.url,
			onComplete: this.onLoad.bind(this)
		}).post(this.options);
	},

	reload: function(){
		this.content.set('html', "Обновление...");
		this.load();
	},

	onLoad: function(json, text){
		if (!json){
			this.content.set('html', '<a href="#" class="reload" onclick="MODS[\'' + this.mod_name + '\'].reload(); return false;">[обновить]</a><br><br>Произошла ошибка!');
			alert(text);
			return;
		}
		if (json.err){
			this.content.set('html', '<a href="#" class="reload" onclick="MODS[\'' + this.mod_name + '\'].reload(); return false;">[обновить]</a><br><br>В доступе отказано!');
		} else {
			this.content.set('html', '<a href="#" class="reload" onclick="MODS[\'' + this.mod_name + '\'].reload(); return false;">[обновить]</a><br><br>' + json.content);
			this.update();
		}
	},

	update: function(){
		var tars = this.content.getElements('textarea');
		for (var i = 0 ; i < tars.length ; i++){
			var tofull = new Element('div', {'class' : 'textarea-to-full', 'html' : 'развернуть',});
			tofull.status = 'min';
			tofull.addEvent('click', function(){
				if (this.status == 'min'){
					this.status = 'max';
					this.set('html', 'свернуть');
					this.setStyle('position', 'fixed');
					this.getParent().getElement('textarea').setStyles({'position' : 'fixed', 'left' : 0, 'top' : 0, 'width' : $(window).getCoordinates()['width'], 'height' : $(window).getCoordinates()['height']});
				} else {
					this.status = 'min';
					this.set('html', 'развенуть');
					this.setStyle('position', 'absolute');
					this.getParent().getElement('textarea').setStyles({'position' : 'relative', 'width' : '100%', 'height' : 'auto'});
				}
			}.bind(tofull));
			tars[i].getParent().adopt(tofull);
			textarea_not_tab(tars[i]);
		}
		var inputs = this.content.getElements('input');
		for (var i = 0 ; i < inputs.length ; i++){
			if (inputs[i].getAttribute('type') == "submit" && inputs[i].getAttribute('confirm')){
				inputs[i].addEvent('click', function(e){
					if (!confirm(this.getAttribute('confirm'))) e.stop();
				}.bind(inputs[i]));
			}
		}
		var forms = this.content.getElements('form');
		for (var i = 0 ; i < forms.length ; i++){
			forms[i].module = this;
			forms[i].validator = new FormValidator(forms[i], {
			'stopOnFailure' : true,
			'evaluateOnSubmit': false,
			'evaluateFieldsOnBlur': false,
			'evaluateFieldsOnChange': false,
			'validateFunction' : function(el){
				if (el.getAttribute('notnull') && el.get('value') == ""){
					if (this.error != "") this.error += "<br>"
					this.error += el.getAttribute('notnull');
					return false;
				}
				return true;
			}.bind(forms[i])
			})
			if (forms[i].getAttribute('table')){
				new Table(forms[i]);
			} else {
				forms[i].submitFunction = this.submit;
				forms[i].addEvent('submit', this.submit.bind(forms[i]));
			}
		}
	},

	submit: function(e){
		this.error = "";
		if (this.validator.validate() === true){
			new Alert("Загрузка!");
			this.removeEvents('submit');
			new Submit(this, this.get('module'), this.get('func'));
			this.addEvent('submit', this.submitFunction.bind(this));
		} else {
			e.stop();
			new Alert(this.error);
		}
	},

	show: function(){
		if (NOW_MOD != ''){
			MODS[NOW_MOD].hide();
		}
		NOW_MOD = this.mod_name;
		this.content.setStyle('display', 'block');

		if (this.type == "opened"){
			$("opened").fireEvent('click');
		}
	},

	hide: function(){
		this.content.setStyle('display', 'none');
	}
});

var Table = new Class({
	
	initialize: function(form){
		this.form = form;
		this.form.addEvent('submit', this.submit.bind(this));
		
		this.number = this.form.getAttribute('count');
		
		this.table = this.form.getParent().getElement('tbody');
		this.table.set('html', '<tr><td>выберите начальные параметры для просмотра таблицы<br>в таблице ' + this.number + ' строк</td></tr>');
		
		var selects = this.form.getElements('select');
		for (var i = 0 ; i < selects.length ; i++){
			switch(selects[i].getAttribute('name')){
				case 'page':
					this.page = selects[i];
					break;
				case 'count':
					this.count = selects[i];
					break;
				case 'sort':
					this.sort = selects[i];
					break;
			}
		}
		
		this.reCount();
		this.count.addEvent('change', this.reCount.bind(this));
	},
	
	reCount: function(){
		var text = "";
		for (var i = 0 ; i < this.number / this.count.get('value') ; i++){
			text += "<option value=\"" + i + "\">" + (i+1) + "</option>";
		}
		this.page.set('html', text);
	},
	
	submit: function(e){
		e.stop();
		new Alert('Загрузка!');
		this.form.set('send', {
			url: ROOT + "/manager/ajax/tables/out/",
			onSuccess: this.onLoad.bind(this)
		});
		this.form.send();
	},
	
	onLoad: function(text){
		var json = JSON.decode(text, true);
		if (!json){
			alert(text)
		} else {
			this.table.set('html', json.res);
		}
	},
	
	draw: function(){
		
	}
	
});

var Ajax = new Class({

	initialize: function(a, module, func, options){
		if(options && options['confirm']){
			if(!confirm(options['confirm'])) return;
		}
		this.a = $(a);
		new Request.JSON({
			url: ROOT + "/manager/ajax/" + module + "/" + func + "/",
			onComplete: this.onLoad.bind(this)
		}).post(options);
	},

	onLoad: function(json, text){
		if (!json){
			alert(text);
		} else {
			JsonRequest(this.a, json);
		}
	}

});


IFrames = 0;

var Submit = new Class({
	
	initialize: function(form, module, func){
		this.form = $(form);

		this.url = ROOT + "/manager/ajax/" + module + "/" + func + "/";

		this.frame = new Element('iframe', {
		'id': 'uploadframe' + ++IFrames,
		'name': 'uploadframe' + IFrames,
		styles: {
		'display': 'none'
		}
		});
		$(document.body).adopt(this.frame);

		this.frame.addEvent('load', this.Request.bind(this));

		this.form.set('method', 'post');
		this.form.set('action', this.url);
		this.form.set('target', this.frame.get('id'));
		this.form.submit();
	},

	Request: function(){
		var doc = this.frame.contentDocument;
		if (!doc && this.frame.contentWindow) doc = this.frame.contentWindow.document;
		if (!doc) doc = this.frame.document;

		var json = JSON.decode(doc.body.innerHTML, true);
		if (!json){
			alert(doc.body.innerHTML);
		} else {
			JsonRequest(this.form, json);
		}
		setTimeout(function(){this.frame.destroy()}.bind(this), 1000);
	}
});