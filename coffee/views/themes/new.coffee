define((require) ->
  # RequireJS loaders
  app = require("app")
  Theme = require("models/theme")
  Themes = require("collections/themes")
  Topbar = require("views/common/topbar")
  EditableTag = require("views/common/editableTag")
  List = require("views/common/list")
  template = require("text!templates/themes/new.html")

  NewThemeView = Backbone.View.extend({
    template: _.template(template)
    events:
      "submit #newTheme" : "submit"

    model: null

    initialize: () ->
      # Inner view initialisation
      @.innerViews = @.innerViews || {}

      if app.cache.Themes is undefined
        # Load our models from the server
        app.cache.Themes = new Themes()
        app.cache.Themes.fetch()

      # Set our inner views. We're using local variables to kick this off
      # because chaining is (marginally) more taxing on the parser - it has to
      # look through each object and it's prototypes before descending.
      topbar = new Topbar({ model: @.model, className: "topbar" })

      topbar.addChildView({
        "#theme-title": new EditableTag({ content: "Enter your theme name here" })
      })

      @.addChildView({
        "#theme-header" : topbar,
        "#html" : new List({ title: 'HTML' })
        "#css" : new List({ title: 'CSS, Sass &amp; Less' })
        "#js" : new List({ title: 'JS &amp; CoffeeScript' })
        "#objects" : new List({ title: 'Object templates' })
      })

    render: () ->
      # Render this view's template
      @.$el.append(@.template({ siteName : app.currentSite.get('name') }))

      @.renderInnerViews()

      # Return our element, mofo. Our parent's probably going to be adding this,
      # right? Right.
      @.$el

    submit: (event) ->
      # Stop the form from actually being sent
      event.preventDefault()

      # Create a new theme model, and set the data from the form
      theme = new Theme()
      theme.set({
        'nme' : $('#themeName').val()
      })

      console.log(theme, theme.isNew(), theme.attributes._id)

      # Attempt to validate and save
      if theme.isValid() is true
        theme.save()
        app.cache.Themes.add(theme)
      else
        console.log("invalid")

  })
)
