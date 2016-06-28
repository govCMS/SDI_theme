/**
 * @file
 * Provides jQuery behaviors.
 */

(function ($) {
  /**
   * Test jQuery by adding a test="test" attribute to all anchor tags.
   */
  Drupal.behaviors.testLinks = {
    attach: function (context, settings) {
      // $('a', context).attr('test', 'test');
    }
  };

})(jQuery);
