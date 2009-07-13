
window.addEvent('domready', function(){
	var forms = $(document.body).getElements('form');
	for (var i = 0 ; i < forms.length ; i++){
		if (forms[i].get('module') && forms[i].get('func')){
			forms[i].submit_status = false;
			forms[i].addEvent('submit', function(){
				if (this.submit_status == false){
					this.submit_status = true;
					new Submit(this, this.get('module'), this.get('func'));
					this.submit_status = false;
				}
			});
		}
	}
});

IFrames = 0;

var Submit = new Class({
	
	initialize: function(form, module, func){
		this.form = $(form);
		this.module = module;
		this.func = func;

		this.url = ROOT + "/ajax/" + module + "/" + func + "/";

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
			JsonRequest(json, doc.body.innerHTML, this.form, this.module, this.func);
		}
		setTimeout(function(){this.frame.destroy()}.bind(this), 1000);
	}
});

function JsonRequest(json, text, form, module, func){
	alert(text);
}
