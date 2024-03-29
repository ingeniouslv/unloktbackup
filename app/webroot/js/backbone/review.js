var ReviewModel = Backbone.Model.extend({
	urlRoot: '/reviews'
});

var ReviewCollection = Backbone.Collection.extend({
	model: ReviewModel,
	url: '/reviews',
	// Parse the feed to accept data from CakePHP structure.
	parse: function(results) {
		// If we have a results.reviews object, return it as the result set.
		if (typeof results.reviews != 'undefined') {
			return results.reviews;
		} else {
			return results;
		}
	}
});

var ReviewView = Backbone.View.extend({
	events: {
		
	},
	initialize: function() {
		this.template = _.template(this.options.template);
		this.collection.on('reset', this.render, this);
		// If our view starts and we don't have any data, make sure to call fetch() on the collection.
		if (!this.collection.length) {
			// this.collection.fetch();
		}
	},
	render: function() {
		var deal_new_limit = (this.collection.length > 0)?this.collection.at(0).get('deal_limit'):10;
		var feed_new_limit = (this.collection.length > 0)?this.collection.at(0).get('feed_limit'):5;
		var review_new_limit = (this.collection.length > 0)?this.collection.at(0).get('review_new_limit'):5;
		this.$el.html(this.template({reviews: this.collection.toJSON(), deal_new_limit: deal_new_limit, feed_new_limit: feed_new_limit, review_new_limit: review_new_limit}));
	}
});