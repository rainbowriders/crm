simplecrmApp.filter('TranslationFilter', function  () {
    return function  (name) {
        var name = name.toUpperCase();
        return 'translation.' + name;
    }
});