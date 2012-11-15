define(["app", "codemirror"], (app, CodeMirror) ->

  CodeEditor = Backbone.View.extend({
    className: 'code-editor',
    events: {}

    codeSettings: {
      lineNumbers: true
    }

    initialize: (properties) ->
      @.innerViews = @.innerViews || {}

      if properties.codeSettings
        @.codeSettings = _.defaults(properties.codeSettings, @.codeSettings)

    render: () ->
      @.CodeMirror = new CodeMirror(@.el, @.codeSettings)

    unbind: () ->
      delete @.CodeMirror

  })

)
