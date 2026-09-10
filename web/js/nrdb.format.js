(function (format, $) {

    format.cost = function (card) {
        return card.cost === null ? 'X' : card.cost;
    };

    format.icon = function (name, options) {
        options = options || {};
        var className = 'icon icon-' + name + (options.className ? ' ' + options.className : '');
        var svg = '<svg class="' + className + '" aria-hidden="true"><use xlink:href="/images/netrunner.svg#icon-' + name + '"></use></svg>';
        return options.fallback ? svg + '<span class="icon-fallback">' + options.fallback + '</span>' : svg;
    };

    format.type = function (card) {
        var type = '<span class="card-type">' + card.type.name + '</span>';
        if (card.keywords)
            type += '<span class="card-keywords">: ' + card.keywords + '</span>';
        if (card.type_code == "agenda")
            type += ' &middot; <span class="card-prop">' + (card.advancement_cost === null ? 'X' : card.advancement_cost) + '/' + card.agenda_points + format.icon('agenda-points') + '</span>';
        if (card.type_code == "identity" && card.side_code == "corp")
            type += ' &middot; <span class="card-prop">' + card.minimum_deck_size + '/' + (card.influence_limit || '&infin;') + '</span>';
        if (card.type_code == "identity" && card.side_code == "runner")
            type += ' &middot; <span class="card-prop">' + card.minimum_deck_size + '/' + (card.influence_limit || '&infin;') + ' ' + card.base_link + format.icon('link', {fallback: 'link'}) + '</span>';
        if (card.type_code == "operation" || card.type_code == "event")
            type += ' &middot; <span class="card-prop">' + format.cost(card) + format.icon('credit', {fallback: 'credit'}) + ('trash_cost' in card ? ' ' + card.trash_cost + format.icon('trash', {fallback: 'trash'}) : '') + '</span>';
        if (card.type_code == "resource" || card.type_code == "hardware")
            type += ' &middot; <span class="card-prop">' + format.cost(card) + format.icon('credit', {fallback: 'credit'}) + '</span>';
        if (card.type_code == "program")
            type += ' &middot; <span class="card-prop">' + format.cost(card) + format.icon('credit', {fallback: 'credit'}) + ' ' + card.memory_cost + format.icon('mu', {fallback: 'memory unit'}) + '</span>';
        if (card.type_code == "asset" || card.type_code == "upgrade")
            type += ' &middot; <span class="card-prop">' + format.cost(card) + format.icon('credit', {fallback: 'credit'}) + ' ' + card.trash_cost + format.icon('trash', {fallback: 'trash'}) + '</span>';
        if (card.type_code == "ice")
            type += ' &middot; <span class="card-prop">' + format.cost(card) + format.icon('credit', {fallback: 'credit'}) + ('trash_cost' in card ? ' ' + card.trash_cost + format.icon('trash', {fallback: 'trash'}) : '') + '</span>';
        return type;
    };

    format.text = function (card) {
        const icons = [
            [/\[subroutine\]/g, 'subroutine', 'Subroutine'],
            [/\[credit\]/g, 'credit', 'Credit'],
            [/\[trash\]/g, 'trash', 'Trash'],
            [/\[click\]/g, 'click', 'Click'],
            [/\[recurring-credit\]/g, 'recurring-credit', 'Recurring credit'],
            [/\[mu\]/g, 'mu', 'Memory unit'],
            [/\[link\]/g, 'link', 'Link'],
            [/\[anarch\]/g, 'anarch', 'Anarch'],
            [/\[criminal\]/g, 'criminal', 'Criminal'],
            [/\[shaper\]/g, 'shaper', 'Shaper'],
            [/\[jinteki\]/g, 'jinteki', 'Jinteki'],
            [/\[haas-bioroid\]/g, 'haas-bioroid', 'Haas Bioroid'],
            [/\[nbn\]/g, 'nbn', 'NBN'],
            [/\[weyland-consortium\]/g, 'weyland-consortium', 'Weyland Consortium'],
            [/\[interrupt\]/g, 'interrupt', 'Interrupt'],
        ];

        let text = icons.reduce((memo, [pattern, icon, title]) => memo.replace(
            pattern,
            format.icon(icon, {fallback: title})
        ), card.text || '');

        text = text.replace(/<errata>(.+)<\/errata>/, '<em><span class="glyphicon glyphicon-alert"></span> $1</em>');

        text = text.split("\n").join('</p><p>');

        return '<p>' + text + '</p>';
    };

})(NRDB.format = {}, jQuery);
