// Generated by CoffeeScript 1.3.3
(function() {

  define(["app", "views/dashboard/main", "views/content/view"], function(app, DashboardView, ContentView) {
    var Router;
    return Router = {
      Router: Backbone.Router.extend({
        routes: {
          "": "showDashboard",
          "content": "showContent",
          "theme": "showheme",
          "settings": "showSettings",
          "*actions": "defaultRoute"
        },
        showDashboard: function() {},
        showContent: function() {},
        showTheme: function() {},
        showSettings: function() {},
        defaultRoute: function(e) {
          if (e === "admin") {
            return this.showDashboard();
          }
          console.log(e);
          return console.log("default");
        }
      })
    };
  });

}).call(this);
