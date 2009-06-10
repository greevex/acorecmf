
var Enter = new Class({

	initialize: function(){
		this.data = {};
		this.form = $('enter_form');
		this.form.addEvent('submit', this.send.bind(this));
	},

	send: function(){
		if (!this.setData()) return;
		new Request.JSON({
			url: ROOT + "/manager/ajax/system/enter/",
			onComplete: this.onRequest.bind(this)
		}).post(this.data);
	},

	setData: function(){
		this.data = {};
		var inputs = this.form.getElements('input')
		for (var i = 0 ; i < inputs.length ; i++){
			if (inputs[i].get('name')){
				if (inputs[i].getProperty('notnull') != null && inputs[i].get('value') == ''){
					if (inputs[i].focus.bind)
					new Alert(inputs[i].getProperty('notnull'), {
					'onClose': inputs[i].focus.bind(inputs[i])
					});
					else
					new Alert(inputs[i].getProperty('notnull'), {
					'onClose': inputs[i].focus
					});
					return false;
				}
				this.data[inputs[i].get('name')] = inputs[i].get('value');
			}
		}
		return true;
	},

	onRequest: function(json, text){
		if (!json){
			alert(text);
			return;
		}
		if (json.res == "entered") {
			window.location = ROOT + "/manager/" + DESIGN;
		} else {
			new Alert("Не верные данные!");
		}
	}

});

window.addEvent('domready', function(){
	new Enter();
});