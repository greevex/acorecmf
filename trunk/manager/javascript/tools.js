var Alert = new Class({

	initialize: function(text, options){
		if (options)
		this.options = options;

		this.container = new Element('div', {
		'class': 'alert',
		'html': text,
		'opacity': 0
		});

		$(document.body).adopt(this.container);

		this.width = this.container.getCoordinates()['width'];
		this.height = this.container.getCoordinates()['height'];

		this.container.setStyles({
		'margin-left': -1 * this.container.getCoordinates()['width'] / 2,
		'margin-top': $(document).getScroll()['y'] - this.container.getCoordinates()['height'] / 2
		});

		var morph1 = new Fx.Morph(this.container, {duration: 'short', onComplete : function(){
			setTimeout(function(){
				morph1.start({'opacity': 0});
				morph1 = new Fx.Morph(this.container, {duration: 'short', onComplete : function(){
					this.Destroy();
				}.bind(this)});
			}.bind(this), 500);
		}.bind(this)});
		morph1.start({'opacity': 0.9});

		window.addEvent('scroll', this.onScroll.bind(this));
		this.container.addEvent('click', this.Destroy.bind(this));
		$(window).scrollTo(0, this.scroll);
	},

	onScroll: function(){
		this.container.setStyles({
		'margin-top': $(document).getScroll()['y'] - this.container.getCoordinates()['height'] / 2
		});
	},

	Destroy: function(){
		this.container.destroy();
		if (this.options && this.options.onClose){
			this.options.onClose();
		}
	}

});