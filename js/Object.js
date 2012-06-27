(function($) {
	
	if (!$.Speedy) $.Speedy	= {};
	
	$.Speedy.Class	= function(Name, Extends, methods) {
		var methods	= ((typeof methods) == 'object') ? methods : Extends,
			extend	= ((typeof Extends) != 'string') ? 'Object' : Extends;
		
		if (typeof methods != 'object') {
			//console.error('Methods must be set as an object');
			throw "Methods must be set as an object";
		}
		
		$.Speedy[Name]	= $.Speedy[extend].Extend(methods);
	};
	
	$.Speedy.New	= function(Class) {
		if ($.Speedy[Class]) {
			var args	= Array.prototype.slice.call(arguments);
			args.shift();
			
			return $.Speedy[Class].Create.apply($.Speedy[Class], args);
		} else {
			console.warn('No speedy ' + Class + ' class found!');
		}
	}
	
	$.Speedy.Object	= (function() {
		var construct	= function() {};
		var me	= this;

		var Create	= function() {
			var me = this, args	= arguments;
			
			var obj	=  function() {
				for (var method in me) {
					this[method]	= me[method];
				}
				
				this.construct.apply(this, args)
				
				return this;
			};
			
			return new obj();
		};

		var Extend	= function(Methods) {
			var publicMe	= {};
			
			if (!publicMe.superclass) {
				publicMe._super	= this;
				//publicMe._super.construct	= this.construct;
			}
				
			for (var key in this) {
				if (key == '_super') {
					continue;
				}
					
				publicMe[key]	= this[key];
			}
			
			if (Methods) {
				for (var key in Methods) {
					if (key == '_super') {
						console.warn('_super property is reserved');
						continue;
					}
					
					publicMe[key]	= Methods[key];
				}
			}
			
			publicMe.Extend	= this.Extend;
			publicMe.Create	= this.Create;	
			
			return publicMe;
		};
		
		return {
			Extend	: Extend,
			Create	: Create,
			construct	: construct
		};
	}) ();
	
	$.fn.Speedy	= function(Class, options) {
		return this.each(function() {
			if ($.Speedy[Class]) {
				return $.Speedy[Class].Create(this, options);
			} else {
				console.warn('No speedy ' + Class + ' class found!');
			}
		})
	}
	
	
	$.Speedy.Class('DeleteLink', {
		
		construct: function(el) {
			var me	= this;
			
			this.el	= $(el);
			this.action	= this.getEl().attr('href');
			this.method	= 'POST';
			this.confirm= this.getEl().data('confirm');
			
			this.getEl().click(function() {
				return me.onClick.apply(me);
			});
		},
	
		getEl: function() {
			return this.el;
		},
		
		onClick: function(evt) {
			var msg	= this.getConfirm();
			if (confirm(msg)) {
				return this.doDelete();
			}
			
			return false;
		},
		
		doDelete: function() {
			var cont	= document.createElement('div');
			cont.setAttribute('style', 'display: none;');
			
			var form	= document.createElement('form');
			form.setAttribute('action', this.action);
			form.setAttribute('method', 'POST');
			
			var submit	= document.createElement('input');
			submit.setAttribute('type', 'submit');
			submit.setAttribute('value', 'Submit');
			
			var input	= document.createElement('input');
			input.setAttribute('type', 'hidden');
			input.setAttribute('value', this.getEl().data('method'));
			input.setAttribute('name', '_method');
			
			$(form).append(input);
			$(form).append(submit);
			$(cont).append(form);
			$('body').append(cont); 
			
			$('form:last').submit();
			return false;
		},
		
		getForm: function() {
			return this.form;
		},
		
		getConfirm: function() {
			return this.confirm;
		}
		
	});
	
	$.Speedy.Class('UpdateLink', {
		
		construct: function(el) {
			var me	= this;
			
			this.el	= $(el);
			this.action	= this.getEl().attr('href');
			this.method	= 'PUT';
			
			this.getEl().click(function() {
				return me.onClick.apply(me);
			});
		},
	
		getEl: function() {
			return this.el;
		},
		
		onClick: function(evt) {
			return this.doUpdate();
		},
		
		doUpdate: function() {
			var cont	= document.createElement('div');
			cont.setAttribute('style', 'display: none;');
			
			var form	= document.createElement('form');
			form.setAttribute('action', this.action);
			form.setAttribute('method', 'POST');
			
			var submit	= document.createElement('input');
			submit.setAttribute('type', 'submit');
			submit.setAttribute('value', 'Submit');
			
			var input	= document.createElement('input');
			input.setAttribute('type', 'hidden');
			input.setAttribute('value', this.getEl().data('method'));
			input.setAttribute('name', '_method');
			
			$(form).append(input);
			$(form).append(submit);
			$(cont).append(form);
			$('body').append(cont); 
			
			$('form:last').submit();
			return false;
		},
		
		getForm: function() {
			return this.form;
		}
		
	});
	
	
	$(document).ready(function() {
		$('a[href]').each(function(index) {
			if ($(this).data('method') == 'delete') {
				return $.Speedy.New('DeleteLink', this)
			} else if ($(this).data('method') == 'put') {
				return $.Speedy.New('UpdateLink', this)
			}
		});
	});
	
})(jQuery);
