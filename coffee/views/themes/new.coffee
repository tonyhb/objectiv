define(["app", "models/theme", "collections/themes", "text!templates/themes/new.html"], (app, Theme, Themes, contentTemplate) ->

  NewThemeView = Backbone.View.extend({
    template: _.template(contentTemplate)
    events: {
      "submit #newTheme" : "submit"
    },

    initialize: () ->
      if app.cache.Themes is undefined
        app.cache.Themes = new Themes()

        # Load our models from the server
        app.cache.Themes.fetch()

    render: () ->
      @.$el.html(@.template({ siteName : app.currentSite.get('name') }))

    submit: (event) ->
      # Stop the form from actually being sent
      event.preventDefault()

      # Create a new theme model, and set the data from the form
      theme = new Theme()
      theme.set({
        'nme' : $('#themeName').val()
        'dsc' : $('#themeDesc').val()
      })

      console.log(theme, theme.isNew(), theme.attributes._id);

      # Attempt to validate and save
      if theme.isValid() is true
        theme.save()
        app.cache.Themes.add(theme)
      else
        console.log("invalid")

  })
)
