import React, { Component } from "react";
import _ from "underscore";
import color from "../helpers/color";

export const ColorWrap = Picker => {
  class ColorPicker extends Component {
    constructor(props) {
      super();

      this.state = {
        ...color.toState(props.color, 0, null)
      };

      this.debounce = _.debounce((fn, data, event) => {
        fn(data, event);
      }, 100);
    }

    componentWillReceiveProps(nextProps) {
      // there were ...color.toState(nextProps.color, this.state.oldHue, this.state.hsv)
      // in old variant. if we put this.state.hsv - plugin works only as uncontrolled
      // component and doesn't update himself if props change
      // there isn't this parameter(this.state.hsv) in the original plugin at github
      this.setState({
        ...color.toState(nextProps.color, this.state.oldHue)
      });
    }

    handleChange = (data, event) => {
      const isValidColor = color.simpleCheckForValidColor(data);
      if (isValidColor) {
        const colors = color.toState(
          data,
          data.h || this.state.oldHue,
          data.hsv || this.state.hsv
        );
        this.setState(colors);
        this.props.onChangeComplete &&
          this.debounce(this.props.onChangeComplete, colors, event);
        this.props.onChange &&
          this.props.onChange(
            {
              ...colors,
              wasChanged: data.wasChanged,
              opacityDragEnd: data.opacityDragEnd
            },
            event
          );
      }
    };

    handleSwatchHover = (data, event) => {
      const isValidColor = color.simpleCheckForValidColor(data);
      if (isValidColor) {
        const colors = color.toState(
          data,
          data.h || this.state.oldHue,
          data.hsv || this.state.hsv
        );
        this.setState(colors);
        this.props.onSwatchHover && this.props.onSwatchHover(colors, event);
      }
    };

    render() {
      const optionalEvents = {};
      if (this.props.onSwatchHover) {
        optionalEvents.onSwatchHover = this.handleSwatchHover;
      }

      return (
        <Picker
          {...this.props}
          {...this.state}
          onChange={this.handleChange}
          {...optionalEvents}
        />
      );
    }
  }

  ColorPicker.propTypes = {
    ...Picker.propTypes
  };

  ColorPicker.defaultProps = {
    ...Picker.defaultProps,
    color: {
      h: 250,
      s: 0.5,
      l: 0.2,
      a: 1
    }
  };

  return ColorPicker;
};

export default ColorWrap;
