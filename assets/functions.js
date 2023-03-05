/**
 * A shorthand function for document.querySelector, which returns the first matching element within the document.
 *
 * @function $
 * @param {string} selector - The selector to use for finding the element.
 * @returns {Element|null} - The first matching element, or null if none is found.
 */
const $ = document.querySelector.bind(document)

/**
 * A shorthand function for document.querySelectorAll, which returns a NodeList of all matching elements within the document.
 *
 * @function $$
 * @param {string} selector - The selector to use for finding the elements.
 * @returns {NodeList} - A NodeList of all matching elements.
 */
const $$ = document.querySelectorAll.bind(document)

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
 * @param {Element} el - The element to add the listener to.
 * @param {Function} callback - The function to execute when the element is clicked.
 */
const addClick = (el, callback) => el && el.addEventListener('click', callback)

/**
 * Sets a cookie with the given object data.
 *
 * @param {string} name - The name to use for the cookie.
 * @param {object} data - The object data to store in the cookie.
 */
const setCookie = (name, data) => {
  const encodedData = encodeURIComponent(JSON.stringify(data))
  document.cookie = `${name}=${encodedData}`
}

/**
 * Gets the value of a cookie with the given name.
 *
 * @param {string} name - The name of the cookie to get.
 * @returns {any} The value of the cookie or undefined if the cookie does not exist.
 */
const getCookie = name => {
  const cookieRegex = new RegExp(`(^|;)\\s*${name}\\s*=\\s*([^;]+)`)
  const cookieMatch = document.cookie.match(cookieRegex)

  if (cookieMatch) {
    const cookieValue = decodeURIComponent(cookieMatch.pop())
    return JSON.parse(cookieValue)
  } else {
    return undefined
  }
}

/**
 * Deletes a cookie with the specified name by setting its expiration date to the past.
 *
 * @param {string} name - The name of the cookie to delete.
 */
const deleteCookie = name => {
  const pastExpirationDate = 'Thu, 01 Jan 1970 00:00:00 GMT'
  document.cookie = `${name}=; expires=${pastExpirationDate}`
}

/**
 * Sends a POST request to the specified URL with form data.
 *
 * @param {string} url - The URL to send the request to.
 *
 * @param {Object|HTMLFormElement} [formData={}]
 *  The form data to include in the request, can be an object or an HTML form element.
 *  If an HTML form element is passed, the FormData will be generated from the form.
 *
 * @param {function} [callback]
 *  An optional callback function to execute when the response is received.
 *  The function will be called with an object that has two properties:
 *    - data: The response data returned from the server.
 *    - code: The HTTP status code returned from the server.
 */
const sendPostRequest = async (url, formData = {}, callback) => {
  let body
  if (formData instanceof HTMLFormElement) {
    body = new FormData(formData)
  } else if (formData instanceof Object) {
    body = new FormData()
    Object.entries(formData).forEach(([key, value]) => body.append(key, value))
  } else {
    console.error('Invalid form data')
    return
  }

  try {
    const response = await fetch(url, { method: 'POST', body })
    const data = await response.json()
    console.log(data)
    if (callback) callback({ data, code: response.status })
  } catch (error) {
    console.error(error)
  }
}
