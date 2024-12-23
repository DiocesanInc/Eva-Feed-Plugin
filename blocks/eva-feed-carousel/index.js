(function () {
  const __ = wp.i18n.__;
  const registerBlockType = wp.blocks.registerBlockType;
  const createElement = wp.element.createElement;

  //   const pluginPath = dpi_eva_feed_vars.plugin_path;

  //   const options = [
  //     { value: "option1", label: "Option 1" },
  //     { value: "option2", label: "Option 2" },
  //     { value: "option3", label: "Option 3" },
  //   ];

  const options = dpi_eva_feed_vars.eva_feed_streams;

  // Register the block
  registerBlockType("dpi-eva-feed/eva-feed-carousel", {
    title: __("EVA Feed Carousel", "dpi-eva-feed"),
    icon: "rss",
    category: "widgets",
    attributes: {
      selectValue: {
        type: "string",
        default: options[0],
      },
    },
    edit: function (props) {
      function updateSelectValue(event) {
        props.setAttributes({ selectValue: event.target.value });
      }

      return createElement(
        "div",
        { className: "eva-feed-block" },
        createElement("h2", null, "EVA Feed"),
        createElement(
          "select",
          { value: props.attributes.selectValue, onChange: updateSelectValue },
          options.map((option) =>
            createElement("option", { value: option.value }, option.label)
          )
        )
      );
    },
    save: function (props) {
      const selectedValue = props.attributes.selectValue;
      const shortcode = `[eva_feed id='${selectedValue}']`;

      return createElement(
        "div",
        { className: props.className },
        createElement("p", null, shortcode)
      );
    },
  });
})();
