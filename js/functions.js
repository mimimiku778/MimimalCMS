/**
 * Shorthand function for document.querySelector, which returns the first matching element within the document.
 *
 * @function $qS
 * @param {string} selector - The selector to use for finding the element.
 * @returns {Element|null} - The first matching element, or null if none is found.
 */
const $qS = document.querySelector.bind(document)

/**
 * Shorthand function for document.querySelectorAll, which returns a NodeList of all matching elements within the document.
 *
 * @function $qSA
 * @param {string} selector - The selector to use for finding the elements.
 * @returns {NodeList} - A NodeList of all matching elements.
 */
const $qSA = document.querySelectorAll.bind(document)

/**
 * Finds an element by its ID.
 *
 * @param {string} id - The ID of the element to find.
 * @returns {Element|null} - The matching element, or null if none is found.
 */
const byId = id => document.getElementById(id)

/**
 * Adds a click event listener to an element, if it exists.
 *
 * @param {Element|null} element - The element to add the listener to.
 * @param {Function} callback - The function to execute when the element is clicked.
 */
const addClick = (element, callback) => element && element.addEventListener('click', callback)