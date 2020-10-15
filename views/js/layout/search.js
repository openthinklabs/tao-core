/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014-2020 (update and modification) Open Assessment Technologies SA;
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'layout/actions', 'ui/searchModal', 'core/store', 'context', 'util/url'], function (
    $,
    actionManager,
    searchModal,
    store,
    context,
    urlHelper
) {
    /**
     * Seach bar component for TAO action bar. It exposes
     * the container, the indexeddb store that manages
     * search results, and @init function
     */
    const searchComponent = {
        container: null,
        searchStore: null,
        init() {
            store('search')
                .then(store => {
                    searchComponent.searchStore = store;
                    initializeEvents();
                    manageSearchStoreUpdate();
                })
                .catch(e => {
                    actionManager.trigger('error', e);
                });
        }
    };

    /**
     * Sets events to init searchModal instance on search and results icons click, enter keypress
     * and ctrl + k shortcut
     */
    function initializeEvents() {
        searchComponent.container = $('.action-bar .search-area');
        const $searchBtn = $('button.icon-find', searchComponent.container);
        const $searchInput = $('input', searchComponent.container);
        const $resultsBtn = $('button.icon-ul', searchComponent.container);

        $searchBtn.off('.searchComponent').on('click.searchComponent', () => createSearchModalInstance());

        $searchInput.off('.searchComponent').on('keypress.searchComponent', e => {
            if (e.which === 13) {
                createSearchModalInstance();
            }
        });

        $resultsBtn.off('.searchComponent').on('click.searchComponent', () => {
            searchComponent.searchStore
                .getItem('criterias')
                .then(storedCriterias => createSearchModalInstance(storedCriterias, false))
                .catch(e => {
                    actionManager.trigger('error', e);
                });
        });

        $(window).on('keydown.searchComponent', e => {
            if (
                $('.action-bar .search-area').closest('.content-panel').css('display') === 'flex' &&
                e.ctrlKey &&
                e.which == 75
            ) {
                e.preventDefault();
                createSearchModalInstance();
            }
        });
    }

    /**
     * Creates a searchModal instance and set up searchStoreUpdate listener to update search component visuals when search store changes
     * @param {string} criterias - stored criterias for the searchComponent to be initialized with
     * @param {boolean} searchOnInit - if datatable request must be triggered on init, or use the stored results instead
     */
    function createSearchModalInstance(criterias, searchOnInit = true) {
        criterias = criterias || { search: $('input', searchComponent.container).val() };
        const url = searchComponent.container.data('url');
        const rootClassUri = decodeURIComponent(urlHelper.parse(url).query.rootNode);
        const searchModalInstance = searchModal({ criterias, url, searchOnInit, rootClassUri });

        searchModalInstance.on('store-updated', manageSearchStoreUpdate);
        searchModalInstance.on('refresh', uri => {
            actionManager.trigger('refresh', { uri });
        });
    }

    /**
     * Callback to searchStoreUpdate event. First checks if current location is the same as the stored one, and if
     * it is not, clears the store. Then requests stored criterias and results if still necessary, and updates view
     */
    function manageSearchStoreUpdate() {
        searchComponent.searchStore
            .getItem('context')
            .then(storedContext => {
                if (storedContext !== context.shownStructure) {
                    searchComponent.searchStore.clear();
                    updateViewAfterSeachStoreUpdate();
                } else {
                    let promises = [];
                    promises.push(searchComponent.searchStore.getItem('criterias'));
                    promises.push(searchComponent.searchStore.getItem('results'));
                    return Promise.all(promises).then(values => {
                        updateViewAfterSeachStoreUpdate(values[0], values[1]);
                    });
                }
            })
            .catch(e => actionManager.trigger('error', e));
    }

    /**
     * Updates template with the received query and results dataset
     * @param {string} storedCriterias - stored search criterias to be used on component creation
     * @param {object} storedSearchResults - stored search results dataset, to display number of saved results on .results-counter
     */
    function updateViewAfterSeachStoreUpdate(storedCriterias, storedSearchResults) {
        const $searchInput = $('input', searchComponent.container);
        const $resultsCounterContainer = $('.results-counter', searchComponent.container);
        const $searchAreaButtonsContainer = $('.search-area-buttons-container', searchComponent.container);

        $searchInput.val(storedCriterias ? storedCriterias.search : '');
        if (storedSearchResults) {
            $searchAreaButtonsContainer.addClass('has-results-counter');
            $resultsCounterContainer.text(storedSearchResults.totalCount > 99 ? '+99' : storedSearchResults.totalCount);
        } else {
            $searchAreaButtonsContainer.removeClass('has-results-counter');
            $resultsCounterContainer.text('');
        }
    }

    return searchComponent;
});
