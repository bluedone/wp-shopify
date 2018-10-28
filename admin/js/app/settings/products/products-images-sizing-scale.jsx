import { SelectControl } from '@wordpress/components';
import React from 'react';
import ReactDOM from 'react-dom';
import to from 'await-to-js';
import toInteger from 'lodash/toInteger';
import { updateSettingProductsImagesSizingScale } from "../../ws/ws-api";
import { showNotice } from "../../notices/notices";
import { showLoader, hideLoader } from "../../utils/utils";
import { toBoolean } from '../../utils/utils';


function scaleTypes() {

  return [
    {
      label: 'None',
      value: false
    },
    {
      label: '2',
      value: 2
    },
    {
      label: '3',
      value: 3
    }
  ];

}

/*

<ProductsImagesSizingScale />

*/
class ProductsImagesSizingScale extends React.Component {

  state = {
    value: WP_Shopify.settings.productsImagesSizingScale === false ? 'none' : WP_Shopify.settings.productsImagesSizingScale,
    valueHasChanged: false,
    submitElement: jQuery("#submitSettings")
  }

  updateValue = newValue => {

    if (newValue !== this.state.value) {
      this.state.valueHasChanged = true;
    }

    this.setState({
      value: newValue
    });

  }

  onProductsImagesSizingScaleBlur = async value => {

    // If selected the same value, just exit
    if ( !this.state.valueHasChanged ) {
      return this.state.value;
    }

    showLoader(this.state.submitElement);

    var [updateError, updateResponse] = await to( updateSettingProductsImagesSizingScale({
      value: toInteger(this.state.value)
    }));

    showNotice(updateError, updateResponse);

    hideLoader(this.state.submitElement);

  }

  render() {

    return (
      <SelectControl
        value={ this.state.value }
        options={ scaleTypes() }
        onChange={ this.updateValue }
        onBlur={ this.onProductsImagesSizingScaleBlur }
        aria-describedby="wps-products-images-sizing-toggle"
        disabled={ !toBoolean(WP_Shopify.settings.productsImagesSizingToggle) }

      />
    );

  }

}


/*

Init color pickers

*/
function initProductsImagesSizingScale() {

  ReactDOM.render(
    <ProductsImagesSizingScale />,
    document.getElementById("wps-settings-products-images-sizing-scale")
  );

}

export {
  initProductsImagesSizingScale
}
