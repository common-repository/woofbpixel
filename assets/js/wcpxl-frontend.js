jQuery(document).ready(function($) {
   var
     skuCookieName = 'sku-cookie'
   , productSku
   , singleProductSku
   , cartPage = $('body').hasClass('woocommerce-cart')
   , checkoutPage = $('body').hasClass('woocommerce-checkout')
   , myAccountPage = $('body').hasClass('woocommerce-account')
   , inOneHour = new Date(new Date().getTime() + 60 * 60 * 1000);
   ;

   // console.log("Custom Thank you Page ID:" + pageId);

    // Archive Product Add to Cart
    $('.add_to_cart_button').on('click', function() {
        productSku = $(this).closest('.product').find('.fpix-sku').text();

        // Check if Cookie exists
        if($.cookie(skuCookieName) !== undefined) {
            // console.log('Cookie exists');

            tempObject = JSON.parse($.cookie(skuCookieName));
            tempObjectValues = tempObject.values;

            object = {
                id: skuCookieName // The ID that will be used as the name of our cookie
                , values : tempObjectValues // Store Product SKUs
            }

            // Our Object before the addition of new SKU
            // console.log('Our object from cookie: \n');
            // console.log(tempObjectValues)
            // console.log('=======================\n');

            // Check if current SKU exists in object array
            // and push Product SKU into object value array
            if(object.values.indexOf(productSku) === -1 ) {
                object.values.push(productSku);
            }

            // Our Object after the addition of new SKU
            // console.log('Our object after SKU addition: \n');
            // console.log(tempObjectValues)
            // console.log('=======================\n');

            // Parse object data through JSON operations
            cookieString = JSON.stringify(object) // Variable that stores JSON stringify from current object
            cookieObject = JSON.parse(cookieString) // Variable that stores JSON parse from current object

            // Update Product Cookie with new SKU values
            Cookies.set(skuCookieName, cookieString, { expires: inOneHour });

        } else {
            // console.log('Cookie doesn\'t exist');

            // Our Object
            object = {
                id: skuCookieName // The ID that will be used as the name of our cookie
                , values : [] // Store Product SKUs
            }

            // Push Product SKU into object value array
            object.values.push(productSku);

            // console.log('Our initial object: \n');
            // console.log(object.values);
            // console.log('=======================\n');

            // Parse object data through JSON operations
            cookieString = JSON.stringify(object) // Variable that stores JSON stringify from current object
            cookieObject = JSON.parse(cookieString) // Variable that stores JSON parse from current object

            // Set Product Cookie with new SKU values
            Cookies.set(skuCookieName, cookieString, { expires: inOneHour });

        }

    });
   //  End of Archive Product add to cart


    // Single Product Add to Cart
    $('.single_add_to_cart_button').on('click', function() {
        singleProductSku = $(this).closest('.entry-summary').find('.fpix-sku').text();

        // Check if Cookie exists
        if($.cookie(skuCookieName) !== undefined) {
            // console.log('Cookie exists');

            tempObject = JSON.parse($.cookie(skuCookieName));
            tempObjectValues = tempObject.values;

            object = {
                id: skuCookieName // The ID that will be used as the name of our cookie
                , values : tempObjectValues // Store Product SKUs
            }

            // Our Object before the addition of new SKU
            // console.log('Our object from cookie: \n');
            // console.log(tempObjectValues)
            // console.log('=======================\n');

            // Check if current SKU exists in object array
            // and push Product SKU into object value array
            if(object.values.indexOf(singleProductSku) === -1 ) {
                object.values.push(singleProductSku);
            }

            // Our Object after the addition of new SKU
            // console.log('Our object after SKU addition: \n');
            // console.log(tempObjectValues)
            // console.log('=======================\n');

            // Parse object data through JSON operations
            cookieString = JSON.stringify(object) // Variable that stores JSON stringify from current object
            cookieObject = JSON.parse(cookieString) // Variable that stores JSON parse from current object

            // Update Product Cookie with new SKU values
            Cookies.set(skuCookieName, cookieString, { expires: inOneHour });

        } else {
            // console.log('Cookie doesn\'t exist');

            // Our Object
            object = {
                id: skuCookieName // The ID that will be used as the name of our cookie
                , values : [] // Store Product SKUs
            }

            // Push Product SKU into object value array
            object.values.push(singleProductSku);

            // console.log('Our initial object: \n');
            // console.log(object.values);
            // console.log('=======================\n');

            // Parse object data through JSON operations
            cookieString = JSON.stringify(object) // Variable that stores JSON stringify from current object
            cookieObject = JSON.parse(cookieString) // Variable that stores JSON parse from current object

            // Set Product Cookie with new SKU values
            Cookies.set(skuCookieName, cookieString, { expires: inOneHour });

        }

    });
    // End of Single Product add to cart


    // Check if Cookie exists and Add all SKUs as URL Params with "?"
    if($.cookie(skuCookieName) !== undefined) {
        // console.log('Cookie IS SET');
        var
          tempObject = JSON.parse($.cookie(skuCookieName))
        , tempObjectValues = tempObject.values
        , tempUrlParams = '';
        ;

        // Our Object before the addition of new SKU
        // console.log('Our object from cookie: \n');
        // console.log(tempObjectValues)
        // console.log('=======================\n');

        // Get all SKUs from Array and transform them into URL Params with ? prepended
        tempObjectValues.forEach(function(element) {
            // console.log(element);
            tempUrlParams += '?' + element;
        });

        // Check if Page is Cart OR Checkout OR My Account Page and change URL Params
        if( cartPage ) {
            // console.log('You are in the Cart Page OR in the Checkout Page OR in the My Account Page');
            window.history.replaceState(null, null, tempUrlParams);
        }

    };

    



});
