/*
 * Naja.js
 * 2.0.0
 *
 * by Jiří Pudil <https://jiripudil.cz>
 */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
	typeof define === 'function' && define.amd ? define(factory) :
	(global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.naja = factory());
}(this, (function () { 'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	(function (factory) {
	  
	  factory();
	}((function () {
	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError("Cannot call a class as a function");
	    }
	  }

	  function _defineProperties(target, props) {
	    for (var i = 0; i < props.length; i++) {
	      var descriptor = props[i];
	      descriptor.enumerable = descriptor.enumerable || false;
	      descriptor.configurable = true;
	      if ("value" in descriptor) descriptor.writable = true;
	      Object.defineProperty(target, descriptor.key, descriptor);
	    }
	  }

	  function _createClass(Constructor, protoProps, staticProps) {
	    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
	    if (staticProps) _defineProperties(Constructor, staticProps);
	    return Constructor;
	  }

	  function _inherits(subClass, superClass) {
	    if (typeof superClass !== "function" && superClass !== null) {
	      throw new TypeError("Super expression must either be null or a function");
	    }

	    subClass.prototype = Object.create(superClass && superClass.prototype, {
	      constructor: {
	        value: subClass,
	        writable: true,
	        configurable: true
	      }
	    });
	    if (superClass) _setPrototypeOf(subClass, superClass);
	  }

	  function _getPrototypeOf(o) {
	    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
	      return o.__proto__ || Object.getPrototypeOf(o);
	    };
	    return _getPrototypeOf(o);
	  }

	  function _setPrototypeOf(o, p) {
	    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
	      o.__proto__ = p;
	      return o;
	    };

	    return _setPrototypeOf(o, p);
	  }

	  function _isNativeReflectConstruct() {
	    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
	    if (Reflect.construct.sham) return false;
	    if (typeof Proxy === "function") return true;

	    try {
	      Date.prototype.toString.call(Reflect.construct(Date, [], function () {}));
	      return true;
	    } catch (e) {
	      return false;
	    }
	  }

	  function _assertThisInitialized(self) {
	    if (self === void 0) {
	      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
	    }

	    return self;
	  }

	  function _possibleConstructorReturn(self, call) {
	    if (call && (typeof call === "object" || typeof call === "function")) {
	      return call;
	    }

	    return _assertThisInitialized(self);
	  }

	  function _createSuper(Derived) {
	    var hasNativeReflectConstruct = _isNativeReflectConstruct();

	    return function _createSuperInternal() {
	      var Super = _getPrototypeOf(Derived),
	          result;

	      if (hasNativeReflectConstruct) {
	        var NewTarget = _getPrototypeOf(this).constructor;

	        result = Reflect.construct(Super, arguments, NewTarget);
	      } else {
	        result = Super.apply(this, arguments);
	      }

	      return _possibleConstructorReturn(this, result);
	    };
	  }

	  function _superPropBase(object, property) {
	    while (!Object.prototype.hasOwnProperty.call(object, property)) {
	      object = _getPrototypeOf(object);
	      if (object === null) break;
	    }

	    return object;
	  }

	  function _get(target, property, receiver) {
	    if (typeof Reflect !== "undefined" && Reflect.get) {
	      _get = Reflect.get;
	    } else {
	      _get = function _get(target, property, receiver) {
	        var base = _superPropBase(target, property);

	        if (!base) return;
	        var desc = Object.getOwnPropertyDescriptor(base, property);

	        if (desc.get) {
	          return desc.get.call(receiver);
	        }

	        return desc.value;
	      };
	    }

	    return _get(target, property, receiver || target);
	  }

	  var Emitter = /*#__PURE__*/function () {
	    function Emitter() {
	      _classCallCheck(this, Emitter);

	      Object.defineProperty(this, 'listeners', {
	        value: {},
	        writable: true,
	        configurable: true
	      });
	    }

	    _createClass(Emitter, [{
	      key: "addEventListener",
	      value: function addEventListener(type, callback) {
	        if (!(type in this.listeners)) {
	          this.listeners[type] = [];
	        }

	        this.listeners[type].push(callback);
	      }
	    }, {
	      key: "removeEventListener",
	      value: function removeEventListener(type, callback) {
	        if (!(type in this.listeners)) {
	          return;
	        }

	        var stack = this.listeners[type];

	        for (var i = 0, l = stack.length; i < l; i++) {
	          if (stack[i] === callback) {
	            stack.splice(i, 1);
	            return;
	          }
	        }
	      }
	    }, {
	      key: "dispatchEvent",
	      value: function dispatchEvent(event) {
	        var _this = this;

	        if (!(event.type in this.listeners)) {
	          return;
	        }

	        var debounce = function debounce(callback) {
	          setTimeout(function () {
	            return callback.call(_this, event);
	          });
	        };

	        var stack = this.listeners[event.type];

	        for (var i = 0, l = stack.length; i < l; i++) {
	          debounce(stack[i]);
	        }

	        return !event.defaultPrevented;
	      }
	    }]);

	    return Emitter;
	  }();

	  var AbortSignal = /*#__PURE__*/function (_Emitter) {
	    _inherits(AbortSignal, _Emitter);

	    var _super = _createSuper(AbortSignal);

	    function AbortSignal() {
	      var _this2;

	      _classCallCheck(this, AbortSignal);

	      _this2 = _super.call(this); // Some versions of babel does not transpile super() correctly for IE <= 10, if the parent
	      // constructor has failed to run, then "this.listeners" will still be undefined and then we call
	      // the parent constructor directly instead as a workaround. For general details, see babel bug:
	      // https://github.com/babel/babel/issues/3041
	      // This hack was added as a fix for the issue described here:
	      // https://github.com/Financial-Times/polyfill-library/pull/59#issuecomment-477558042

	      if (!_this2.listeners) {
	        Emitter.call(_assertThisInitialized(_this2));
	      } // Compared to assignment, Object.defineProperty makes properties non-enumerable by default and
	      // we want Object.keys(new AbortController().signal) to be [] for compat with the native impl


	      Object.defineProperty(_assertThisInitialized(_this2), 'aborted', {
	        value: false,
	        writable: true,
	        configurable: true
	      });
	      Object.defineProperty(_assertThisInitialized(_this2), 'onabort', {
	        value: null,
	        writable: true,
	        configurable: true
	      });
	      return _this2;
	    }

	    _createClass(AbortSignal, [{
	      key: "toString",
	      value: function toString() {
	        return '[object AbortSignal]';
	      }
	    }, {
	      key: "dispatchEvent",
	      value: function dispatchEvent(event) {
	        if (event.type === 'abort') {
	          this.aborted = true;

	          if (typeof this.onabort === 'function') {
	            this.onabort.call(this, event);
	          }
	        }

	        _get(_getPrototypeOf(AbortSignal.prototype), "dispatchEvent", this).call(this, event);
	      }
	    }]);

	    return AbortSignal;
	  }(Emitter);
	  var AbortController = /*#__PURE__*/function () {
	    function AbortController() {
	      _classCallCheck(this, AbortController);

	      // Compared to assignment, Object.defineProperty makes properties non-enumerable by default and
	      // we want Object.keys(new AbortController()) to be [] for compat with the native impl
	      Object.defineProperty(this, 'signal', {
	        value: new AbortSignal(),
	        writable: true,
	        configurable: true
	      });
	    }

	    _createClass(AbortController, [{
	      key: "abort",
	      value: function abort() {
	        var event;

	        try {
	          event = new Event('abort');
	        } catch (e) {
	          if (typeof document !== 'undefined') {
	            if (!document.createEvent) {
	              // For Internet Explorer 8:
	              event = document.createEventObject();
	              event.type = 'abort';
	            } else {
	              // For Internet Explorer 11:
	              event = document.createEvent('Event');
	              event.initEvent('abort', false, false);
	            }
	          } else {
	            // Fallback where document isn't available:
	            event = {
	              type: 'abort',
	              bubbles: false,
	              cancelable: false
	            };
	          }
	        }

	        this.signal.dispatchEvent(event);
	      }
	    }, {
	      key: "toString",
	      value: function toString() {
	        return '[object AbortController]';
	      }
	    }]);

	    return AbortController;
	  }();

	  if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
	    // These are necessary to make sure that we get correct output for:
	    // Object.prototype.toString.call(new AbortController())
	    AbortController.prototype[Symbol.toStringTag] = 'AbortController';
	    AbortSignal.prototype[Symbol.toStringTag] = 'AbortSignal';
	  }

	  function polyfillNeeded(self) {
	    if (self.__FORCE_INSTALL_ABORTCONTROLLER_POLYFILL) {
	      console.log('__FORCE_INSTALL_ABORTCONTROLLER_POLYFILL=true is set, will force install polyfill');
	      return true;
	    } // Note that the "unfetch" minimal fetch polyfill defines fetch() without
	    // defining window.Request, and this polyfill need to work on top of unfetch
	    // so the below feature detection needs the !self.AbortController part.
	    // The Request.prototype check is also needed because Safari versions 11.1.2
	    // up to and including 12.1.x has a window.AbortController present but still
	    // does NOT correctly implement abortable fetch:
	    // https://bugs.webkit.org/show_bug.cgi?id=174980#c2


	    return typeof self.Request === 'function' && !self.Request.prototype.hasOwnProperty('signal') || !self.AbortController;
	  }

	  /**
	   * Note: the "fetch.Request" default value is available for fetch imported from
	   * the "node-fetch" package and not in browsers. This is OK since browsers
	   * will be importing umd-polyfill.js from that path "self" is passed the
	   * decorator so the default value will not be used (because browsers that define
	   * fetch also has Request). One quirky setup where self.fetch exists but
	   * self.Request does not is when the "unfetch" minimal fetch polyfill is used
	   * on top of IE11; for this case the browser will try to use the fetch.Request
	   * default value which in turn will be undefined but then then "if (Request)"
	   * will ensure that you get a patched fetch but still no Request (as expected).
	   * @param {fetch, Request = fetch.Request}
	   * @returns {fetch: abortableFetch, Request: AbortableRequest}
	   */

	  function abortableFetchDecorator(patchTargets) {
	    if ('function' === typeof patchTargets) {
	      patchTargets = {
	        fetch: patchTargets
	      };
	    }

	    var _patchTargets = patchTargets,
	        fetch = _patchTargets.fetch,
	        _patchTargets$Request = _patchTargets.Request,
	        NativeRequest = _patchTargets$Request === void 0 ? fetch.Request : _patchTargets$Request,
	        NativeAbortController = _patchTargets.AbortController,
	        _patchTargets$__FORCE = _patchTargets.__FORCE_INSTALL_ABORTCONTROLLER_POLYFILL,
	        __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL = _patchTargets$__FORCE === void 0 ? false : _patchTargets$__FORCE;

	    if (!polyfillNeeded({
	      fetch: fetch,
	      Request: NativeRequest,
	      AbortController: NativeAbortController,
	      __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL: __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL
	    })) {
	      return {
	        fetch: fetch,
	        Request: Request
	      };
	    }

	    var Request = NativeRequest; // Note that the "unfetch" minimal fetch polyfill defines fetch() without
	    // defining window.Request, and this polyfill need to work on top of unfetch
	    // hence we only patch it if it's available. Also we don't patch it if signal
	    // is already available on the Request prototype because in this case support
	    // is present and the patching below can cause a crash since it assigns to
	    // request.signal which is technically a read-only property. This latter error
	    // happens when you run the main5.js node-fetch example in the repo
	    // "abortcontroller-polyfill-examples". The exact error is:
	    //   request.signal = init.signal;
	    //   ^
	    // TypeError: Cannot set property signal of #<Request> which has only a getter

	    if (Request && !Request.prototype.hasOwnProperty('signal') || __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL) {
	      Request = function Request(input, init) {
	        var signal;

	        if (init && init.signal) {
	          signal = init.signal; // Never pass init.signal to the native Request implementation when the polyfill has
	          // been installed because if we're running on top of a browser with a
	          // working native AbortController (i.e. the polyfill was installed due to
	          // __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL being set), then passing our
	          // fake AbortSignal to the native fetch will trigger:
	          // TypeError: Failed to construct 'Request': member signal is not of type AbortSignal.

	          delete init.signal;
	        }

	        var request = new NativeRequest(input, init);

	        if (signal) {
	          Object.defineProperty(request, 'signal', {
	            writable: false,
	            enumerable: false,
	            configurable: true,
	            value: signal
	          });
	        }

	        return request;
	      };

	      Request.prototype = NativeRequest.prototype;
	    }

	    var realFetch = fetch;

	    var abortableFetch = function abortableFetch(input, init) {
	      var signal = Request && Request.prototype.isPrototypeOf(input) ? input.signal : init ? init.signal : undefined;

	      if (signal) {
	        var abortError;

	        try {
	          abortError = new DOMException('Aborted', 'AbortError');
	        } catch (err) {
	          // IE 11 does not support calling the DOMException constructor, use a
	          // regular error object on it instead.
	          abortError = new Error('Aborted');
	          abortError.name = 'AbortError';
	        } // Return early if already aborted, thus avoiding making an HTTP request


	        if (signal.aborted) {
	          return Promise.reject(abortError);
	        } // Turn an event into a promise, reject it once `abort` is dispatched


	        var cancellation = new Promise(function (_, reject) {
	          signal.addEventListener('abort', function () {
	            return reject(abortError);
	          }, {
	            once: true
	          });
	        });

	        if (init && init.signal) {
	          // Never pass .signal to the native implementation when the polyfill has
	          // been installed because if we're running on top of a browser with a
	          // working native AbortController (i.e. the polyfill was installed due to
	          // __FORCE_INSTALL_ABORTCONTROLLER_POLYFILL being set), then passing our
	          // fake AbortSignal to the native fetch will trigger:
	          // TypeError: Failed to execute 'fetch' on 'Window': member signal is not of type AbortSignal.
	          delete init.signal;
	        } // Return the fastest promise (don't need to wait for request to finish)


	        return Promise.race([cancellation, realFetch(input, init)]);
	      }

	      return realFetch(input, init);
	    };

	    return {
	      fetch: abortableFetch,
	      Request: Request
	    };
	  }

	  (function (self) {

	    if (!polyfillNeeded(self)) {
	      return;
	    }

	    if (!self.fetch) {
	      console.warn('fetch() is not available, cannot install abortcontroller-polyfill');
	      return;
	    }

	    var _abortableFetch = abortableFetchDecorator(self),
	        fetch = _abortableFetch.fetch,
	        Request = _abortableFetch.Request;

	    self.fetch = fetch;
	    self.Request = Request;
	    Object.defineProperty(self, 'AbortController', {
	      writable: true,
	      enumerable: false,
	      configurable: true,
	      value: AbortController
	    });
	    Object.defineProperty(self, 'AbortSignal', {
	      writable: true,
	      enumerable: false,
	      configurable: true,
	      value: AbortSignal
	    });
	  })(typeof self !== 'undefined' ? self : commonjsGlobal);

	})));

	/**
	 * @author Toru Nagashima <https://github.com/mysticatea>
	 * @copyright 2015 Toru Nagashima. All rights reserved.
	 * See LICENSE file in root directory for full license.
	 */

	/**
	 * @typedef {object} PrivateData
	 * @property {EventTarget} eventTarget The event target.
	 * @property {{type:string}} event The original event object.
	 * @property {number} eventPhase The current event phase.
	 * @property {EventTarget|null} currentTarget The current event target.
	 * @property {boolean} canceled The flag to prevent default.
	 * @property {boolean} stopped The flag to stop propagation.
	 * @property {boolean} immediateStopped The flag to stop propagation immediately.
	 * @property {Function|null} passiveListener The listener if the current listener is passive. Otherwise this is null.
	 * @property {number} timeStamp The unix time.
	 * @private
	 */

	/**
	 * Private data for event wrappers.
	 * @type {WeakMap<Event, PrivateData>}
	 * @private
	 */
	const privateData = new WeakMap();
	/**
	 * Cache for wrapper classes.
	 * @type {WeakMap<Object, Function>}
	 * @private
	 */

	const wrappers = new WeakMap();
	/**
	 * Get private data.
	 * @param {Event} event The event object to get private data.
	 * @returns {PrivateData} The private data of the event.
	 * @private
	 */

	function pd(event) {
	  const retv = privateData.get(event);
	  console.assert(retv != null, "'this' is expected an Event object, but got", event);
	  return retv;
	}
	/**
	 * https://dom.spec.whatwg.org/#set-the-canceled-flag
	 * @param data {PrivateData} private data.
	 */


	function setCancelFlag(data) {
	  if (data.passiveListener != null) {
	    if (typeof console !== "undefined" && typeof console.error === "function") {
	      console.error("Unable to preventDefault inside passive event listener invocation.", data.passiveListener);
	    }

	    return;
	  }

	  if (!data.event.cancelable) {
	    return;
	  }

	  data.canceled = true;

	  if (typeof data.event.preventDefault === "function") {
	    data.event.preventDefault();
	  }
	}
	/**
	 * @see https://dom.spec.whatwg.org/#interface-event
	 * @private
	 */

	/**
	 * The event wrapper.
	 * @constructor
	 * @param {EventTarget} eventTarget The event target of this dispatching.
	 * @param {Event|{type:string}} event The original event to wrap.
	 */


	function Event$1(eventTarget, event) {
	  privateData.set(this, {
	    eventTarget,
	    event,
	    eventPhase: 2,
	    currentTarget: eventTarget,
	    canceled: false,
	    stopped: false,
	    immediateStopped: false,
	    passiveListener: null,
	    timeStamp: event.timeStamp || Date.now()
	  }); // https://heycam.github.io/webidl/#Unforgeable

	  Object.defineProperty(this, "isTrusted", {
	    value: false,
	    enumerable: true
	  }); // Define accessors

	  const keys = Object.keys(event);

	  for (let i = 0; i < keys.length; ++i) {
	    const key = keys[i];

	    if (!(key in this)) {
	      Object.defineProperty(this, key, defineRedirectDescriptor(key));
	    }
	  }
	} // Should be enumerable, but class methods are not enumerable.


	Event$1.prototype = {
	  /**
	   * The type of this event.
	   * @type {string}
	   */
	  get type() {
	    return pd(this).event.type;
	  },

	  /**
	   * The target of this event.
	   * @type {EventTarget}
	   */
	  get target() {
	    return pd(this).eventTarget;
	  },

	  /**
	   * The target of this event.
	   * @type {EventTarget}
	   */
	  get currentTarget() {
	    return pd(this).currentTarget;
	  },

	  /**
	   * @returns {EventTarget[]} The composed path of this event.
	   */
	  composedPath() {
	    const currentTarget = pd(this).currentTarget;

	    if (currentTarget == null) {
	      return [];
	    }

	    return [currentTarget];
	  },

	  /**
	   * Constant of NONE.
	   * @type {number}
	   */
	  get NONE() {
	    return 0;
	  },

	  /**
	   * Constant of CAPTURING_PHASE.
	   * @type {number}
	   */
	  get CAPTURING_PHASE() {
	    return 1;
	  },

	  /**
	   * Constant of AT_TARGET.
	   * @type {number}
	   */
	  get AT_TARGET() {
	    return 2;
	  },

	  /**
	   * Constant of BUBBLING_PHASE.
	   * @type {number}
	   */
	  get BUBBLING_PHASE() {
	    return 3;
	  },

	  /**
	   * The target of this event.
	   * @type {number}
	   */
	  get eventPhase() {
	    return pd(this).eventPhase;
	  },

	  /**
	   * Stop event bubbling.
	   * @returns {void}
	   */
	  stopPropagation() {
	    const data = pd(this);
	    data.stopped = true;

	    if (typeof data.event.stopPropagation === "function") {
	      data.event.stopPropagation();
	    }
	  },

	  /**
	   * Stop event bubbling.
	   * @returns {void}
	   */
	  stopImmediatePropagation() {
	    const data = pd(this);
	    data.stopped = true;
	    data.immediateStopped = true;

	    if (typeof data.event.stopImmediatePropagation === "function") {
	      data.event.stopImmediatePropagation();
	    }
	  },

	  /**
	   * The flag to be bubbling.
	   * @type {boolean}
	   */
	  get bubbles() {
	    return Boolean(pd(this).event.bubbles);
	  },

	  /**
	   * The flag to be cancelable.
	   * @type {boolean}
	   */
	  get cancelable() {
	    return Boolean(pd(this).event.cancelable);
	  },

	  /**
	   * Cancel this event.
	   * @returns {void}
	   */
	  preventDefault() {
	    setCancelFlag(pd(this));
	  },

	  /**
	   * The flag to indicate cancellation state.
	   * @type {boolean}
	   */
	  get defaultPrevented() {
	    return pd(this).canceled;
	  },

	  /**
	   * The flag to be composed.
	   * @type {boolean}
	   */
	  get composed() {
	    return Boolean(pd(this).event.composed);
	  },

	  /**
	   * The unix time of this event.
	   * @type {number}
	   */
	  get timeStamp() {
	    return pd(this).timeStamp;
	  },

	  /**
	   * The target of this event.
	   * @type {EventTarget}
	   * @deprecated
	   */
	  get srcElement() {
	    return pd(this).eventTarget;
	  },

	  /**
	   * The flag to stop event bubbling.
	   * @type {boolean}
	   * @deprecated
	   */
	  get cancelBubble() {
	    return pd(this).stopped;
	  },

	  set cancelBubble(value) {
	    if (!value) {
	      return;
	    }

	    const data = pd(this);
	    data.stopped = true;

	    if (typeof data.event.cancelBubble === "boolean") {
	      data.event.cancelBubble = true;
	    }
	  },

	  /**
	   * The flag to indicate cancellation state.
	   * @type {boolean}
	   * @deprecated
	   */
	  get returnValue() {
	    return !pd(this).canceled;
	  },

	  set returnValue(value) {
	    if (!value) {
	      setCancelFlag(pd(this));
	    }
	  },

	  /**
	   * Initialize this event object. But do nothing under event dispatching.
	   * @param {string} type The event type.
	   * @param {boolean} [bubbles=false] The flag to be possible to bubble up.
	   * @param {boolean} [cancelable=false] The flag to be possible to cancel.
	   * @deprecated
	   */
	  initEvent() {// Do nothing.
	  }

	}; // `constructor` is not enumerable.

	Object.defineProperty(Event$1.prototype, "constructor", {
	  value: Event$1,
	  configurable: true,
	  writable: true
	}); // Ensure `event instanceof window.Event` is `true`.

	if (typeof window !== "undefined" && typeof window.Event !== "undefined") {
	  Object.setPrototypeOf(Event$1.prototype, window.Event.prototype); // Make association for wrappers.

	  wrappers.set(window.Event.prototype, Event$1);
	}
	/**
	 * Get the property descriptor to redirect a given property.
	 * @param {string} key Property name to define property descriptor.
	 * @returns {PropertyDescriptor} The property descriptor to redirect the property.
	 * @private
	 */


	function defineRedirectDescriptor(key) {
	  return {
	    get() {
	      return pd(this).event[key];
	    },

	    set(value) {
	      pd(this).event[key] = value;
	    },

	    configurable: true,
	    enumerable: true
	  };
	}
	/**
	 * Get the property descriptor to call a given method property.
	 * @param {string} key Property name to define property descriptor.
	 * @returns {PropertyDescriptor} The property descriptor to call the method property.
	 * @private
	 */


	function defineCallDescriptor(key) {
	  return {
	    value() {
	      const event = pd(this).event;
	      return event[key].apply(event, arguments);
	    },

	    configurable: true,
	    enumerable: true
	  };
	}
	/**
	 * Define new wrapper class.
	 * @param {Function} BaseEvent The base wrapper class.
	 * @param {Object} proto The prototype of the original event.
	 * @returns {Function} The defined wrapper class.
	 * @private
	 */


	function defineWrapper(BaseEvent, proto) {
	  const keys = Object.keys(proto);

	  if (keys.length === 0) {
	    return BaseEvent;
	  }
	  /** CustomEvent */


	  function CustomEvent(eventTarget, event) {
	    BaseEvent.call(this, eventTarget, event);
	  }

	  CustomEvent.prototype = Object.create(BaseEvent.prototype, {
	    constructor: {
	      value: CustomEvent,
	      configurable: true,
	      writable: true
	    }
	  }); // Define accessors.

	  for (let i = 0; i < keys.length; ++i) {
	    const key = keys[i];

	    if (!(key in BaseEvent.prototype)) {
	      const descriptor = Object.getOwnPropertyDescriptor(proto, key);
	      const isFunc = typeof descriptor.value === "function";
	      Object.defineProperty(CustomEvent.prototype, key, isFunc ? defineCallDescriptor(key) : defineRedirectDescriptor(key));
	    }
	  }

	  return CustomEvent;
	}
	/**
	 * Get the wrapper class of a given prototype.
	 * @param {Object} proto The prototype of the original event to get its wrapper.
	 * @returns {Function} The wrapper class.
	 * @private
	 */


	function getWrapper(proto) {
	  if (proto == null || proto === Object.prototype) {
	    return Event$1;
	  }

	  let wrapper = wrappers.get(proto);

	  if (wrapper == null) {
	    wrapper = defineWrapper(getWrapper(Object.getPrototypeOf(proto)), proto);
	    wrappers.set(proto, wrapper);
	  }

	  return wrapper;
	}
	/**
	 * Wrap a given event to management a dispatching.
	 * @param {EventTarget} eventTarget The event target of this dispatching.
	 * @param {Object} event The event to wrap.
	 * @returns {Event} The wrapper instance.
	 * @private
	 */


	function wrapEvent(eventTarget, event) {
	  const Wrapper = getWrapper(Object.getPrototypeOf(event));
	  return new Wrapper(eventTarget, event);
	}
	/**
	 * Get the immediateStopped flag of a given event.
	 * @param {Event} event The event to get.
	 * @returns {boolean} The flag to stop propagation immediately.
	 * @private
	 */


	function isStopped(event) {
	  return pd(event).immediateStopped;
	}
	/**
	 * Set the current event phase of a given event.
	 * @param {Event} event The event to set current target.
	 * @param {number} eventPhase New event phase.
	 * @returns {void}
	 * @private
	 */


	function setEventPhase(event, eventPhase) {
	  pd(event).eventPhase = eventPhase;
	}
	/**
	 * Set the current target of a given event.
	 * @param {Event} event The event to set current target.
	 * @param {EventTarget|null} currentTarget New current target.
	 * @returns {void}
	 * @private
	 */


	function setCurrentTarget(event, currentTarget) {
	  pd(event).currentTarget = currentTarget;
	}
	/**
	 * Set a passive listener of a given event.
	 * @param {Event} event The event to set current target.
	 * @param {Function|null} passiveListener New passive listener.
	 * @returns {void}
	 * @private
	 */


	function setPassiveListener(event, passiveListener) {
	  pd(event).passiveListener = passiveListener;
	}
	/**
	 * @typedef {object} ListenerNode
	 * @property {Function} listener
	 * @property {1|2|3} listenerType
	 * @property {boolean} passive
	 * @property {boolean} once
	 * @property {ListenerNode|null} next
	 * @private
	 */

	/**
	 * @type {WeakMap<object, Map<string, ListenerNode>>}
	 * @private
	 */


	const listenersMap = new WeakMap(); // Listener types

	const CAPTURE = 1;
	const BUBBLE = 2;
	const ATTRIBUTE = 3;
	/**
	 * Check whether a given value is an object or not.
	 * @param {any} x The value to check.
	 * @returns {boolean} `true` if the value is an object.
	 */

	function isObject(x) {
	  return x !== null && typeof x === "object"; //eslint-disable-line no-restricted-syntax
	}
	/**
	 * Get listeners.
	 * @param {EventTarget} eventTarget The event target to get.
	 * @returns {Map<string, ListenerNode>} The listeners.
	 * @private
	 */


	function getListeners(eventTarget) {
	  const listeners = listenersMap.get(eventTarget);

	  if (listeners == null) {
	    throw new TypeError("'this' is expected an EventTarget object, but got another value.");
	  }

	  return listeners;
	}
	/**
	 * Get the property descriptor for the event attribute of a given event.
	 * @param {string} eventName The event name to get property descriptor.
	 * @returns {PropertyDescriptor} The property descriptor.
	 * @private
	 */


	function defineEventAttributeDescriptor(eventName) {
	  return {
	    get() {
	      const listeners = getListeners(this);
	      let node = listeners.get(eventName);

	      while (node != null) {
	        if (node.listenerType === ATTRIBUTE) {
	          return node.listener;
	        }

	        node = node.next;
	      }

	      return null;
	    },

	    set(listener) {
	      if (typeof listener !== "function" && !isObject(listener)) {
	        listener = null; // eslint-disable-line no-param-reassign
	      }

	      const listeners = getListeners(this); // Traverse to the tail while removing old value.

	      let prev = null;
	      let node = listeners.get(eventName);

	      while (node != null) {
	        if (node.listenerType === ATTRIBUTE) {
	          // Remove old value.
	          if (prev !== null) {
	            prev.next = node.next;
	          } else if (node.next !== null) {
	            listeners.set(eventName, node.next);
	          } else {
	            listeners.delete(eventName);
	          }
	        } else {
	          prev = node;
	        }

	        node = node.next;
	      } // Add new value.


	      if (listener !== null) {
	        const newNode = {
	          listener,
	          listenerType: ATTRIBUTE,
	          passive: false,
	          once: false,
	          next: null
	        };

	        if (prev === null) {
	          listeners.set(eventName, newNode);
	        } else {
	          prev.next = newNode;
	        }
	      }
	    },

	    configurable: true,
	    enumerable: true
	  };
	}
	/**
	 * Define an event attribute (e.g. `eventTarget.onclick`).
	 * @param {Object} eventTargetPrototype The event target prototype to define an event attrbite.
	 * @param {string} eventName The event name to define.
	 * @returns {void}
	 */


	function defineEventAttribute(eventTargetPrototype, eventName) {
	  Object.defineProperty(eventTargetPrototype, `on${eventName}`, defineEventAttributeDescriptor(eventName));
	}
	/**
	 * Define a custom EventTarget with event attributes.
	 * @param {string[]} eventNames Event names for event attributes.
	 * @returns {EventTarget} The custom EventTarget.
	 * @private
	 */


	function defineCustomEventTarget(eventNames) {
	  /** CustomEventTarget */
	  function CustomEventTarget() {
	    EventTarget$1.call(this);
	  }

	  CustomEventTarget.prototype = Object.create(EventTarget$1.prototype, {
	    constructor: {
	      value: CustomEventTarget,
	      configurable: true,
	      writable: true
	    }
	  });

	  for (let i = 0; i < eventNames.length; ++i) {
	    defineEventAttribute(CustomEventTarget.prototype, eventNames[i]);
	  }

	  return CustomEventTarget;
	}
	/**
	 * EventTarget.
	 *
	 * - This is constructor if no arguments.
	 * - This is a function which returns a CustomEventTarget constructor if there are arguments.
	 *
	 * For example:
	 *
	 *     class A extends EventTarget {}
	 *     class B extends EventTarget("message") {}
	 *     class C extends EventTarget("message", "error") {}
	 *     class D extends EventTarget(["message", "error"]) {}
	 */


	function EventTarget$1() {
	  /*eslint-disable consistent-return */
	  if (this instanceof EventTarget$1) {
	    listenersMap.set(this, new Map());
	    return;
	  }

	  if (arguments.length === 1 && Array.isArray(arguments[0])) {
	    return defineCustomEventTarget(arguments[0]);
	  }

	  if (arguments.length > 0) {
	    const types = new Array(arguments.length);

	    for (let i = 0; i < arguments.length; ++i) {
	      types[i] = arguments[i];
	    }

	    return defineCustomEventTarget(types);
	  }

	  throw new TypeError("Cannot call a class as a function");
	  /*eslint-enable consistent-return */
	} // Should be enumerable, but class methods are not enumerable.


	EventTarget$1.prototype = {
	  /**
	   * Add a given listener to this event target.
	   * @param {string} eventName The event name to add.
	   * @param {Function} listener The listener to add.
	   * @param {boolean|{capture?:boolean,passive?:boolean,once?:boolean}} [options] The options for this listener.
	   * @returns {void}
	   */
	  addEventListener(eventName, listener, options) {
	    if (listener == null) {
	      return;
	    }

	    if (typeof listener !== "function" && !isObject(listener)) {
	      throw new TypeError("'listener' should be a function or an object.");
	    }

	    const listeners = getListeners(this);
	    const optionsIsObj = isObject(options);
	    const capture = optionsIsObj ? Boolean(options.capture) : Boolean(options);
	    const listenerType = capture ? CAPTURE : BUBBLE;
	    const newNode = {
	      listener,
	      listenerType,
	      passive: optionsIsObj && Boolean(options.passive),
	      once: optionsIsObj && Boolean(options.once),
	      next: null
	    }; // Set it as the first node if the first node is null.

	    let node = listeners.get(eventName);

	    if (node === undefined) {
	      listeners.set(eventName, newNode);
	      return;
	    } // Traverse to the tail while checking duplication..


	    let prev = null;

	    while (node != null) {
	      if (node.listener === listener && node.listenerType === listenerType) {
	        // Should ignore duplication.
	        return;
	      }

	      prev = node;
	      node = node.next;
	    } // Add it.


	    prev.next = newNode;
	  },

	  /**
	   * Remove a given listener from this event target.
	   * @param {string} eventName The event name to remove.
	   * @param {Function} listener The listener to remove.
	   * @param {boolean|{capture?:boolean,passive?:boolean,once?:boolean}} [options] The options for this listener.
	   * @returns {void}
	   */
	  removeEventListener(eventName, listener, options) {
	    if (listener == null) {
	      return;
	    }

	    const listeners = getListeners(this);
	    const capture = isObject(options) ? Boolean(options.capture) : Boolean(options);
	    const listenerType = capture ? CAPTURE : BUBBLE;
	    let prev = null;
	    let node = listeners.get(eventName);

	    while (node != null) {
	      if (node.listener === listener && node.listenerType === listenerType) {
	        if (prev !== null) {
	          prev.next = node.next;
	        } else if (node.next !== null) {
	          listeners.set(eventName, node.next);
	        } else {
	          listeners.delete(eventName);
	        }

	        return;
	      }

	      prev = node;
	      node = node.next;
	    }
	  },

	  /**
	   * Dispatch a given event.
	   * @param {Event|{type:string}} event The event to dispatch.
	   * @returns {boolean} `false` if canceled.
	   */
	  dispatchEvent(event) {
	    if (event == null || typeof event.type !== "string") {
	      throw new TypeError('"event.type" should be a string.');
	    } // If listeners aren't registered, terminate.


	    const listeners = getListeners(this);
	    const eventName = event.type;
	    let node = listeners.get(eventName);

	    if (node == null) {
	      return true;
	    } // Since we cannot rewrite several properties, so wrap object.


	    const wrappedEvent = wrapEvent(this, event); // This doesn't process capturing phase and bubbling phase.
	    // This isn't participating in a tree.

	    let prev = null;

	    while (node != null) {
	      // Remove this listener if it's once
	      if (node.once) {
	        if (prev !== null) {
	          prev.next = node.next;
	        } else if (node.next !== null) {
	          listeners.set(eventName, node.next);
	        } else {
	          listeners.delete(eventName);
	        }
	      } else {
	        prev = node;
	      } // Call this listener


	      setPassiveListener(wrappedEvent, node.passive ? node.listener : null);

	      if (typeof node.listener === "function") {
	        try {
	          node.listener.call(this, wrappedEvent);
	        } catch (err) {
	          if (typeof console !== "undefined" && typeof console.error === "function") {
	            console.error(err);
	          }
	        }
	      } else if (node.listenerType !== ATTRIBUTE && typeof node.listener.handleEvent === "function") {
	        node.listener.handleEvent(wrappedEvent);
	      } // Break if `event.stopImmediatePropagation` was called.


	      if (isStopped(wrappedEvent)) {
	        break;
	      }

	      node = node.next;
	    }

	    setPassiveListener(wrappedEvent, null);
	    setEventPhase(wrappedEvent, 0);
	    setCurrentTarget(wrappedEvent, null);
	    return !wrappedEvent.defaultPrevented;
	  }

	}; // `constructor` is not enumerable.

	Object.defineProperty(EventTarget$1.prototype, "constructor", {
	  value: EventTarget$1,
	  configurable: true,
	  writable: true
	}); // Ensure `eventTarget instanceof window.EventTarget` is `true`.

	if (typeof window !== "undefined" && typeof window.EventTarget !== "undefined") {
	  Object.setPrototypeOf(EventTarget$1.prototype, window.EventTarget.prototype);
	}

	// https://bugs.webkit.org/show_bug.cgi?id=174980

	try {
	  new window.EventTarget();
	} catch (error) {
	  window.EventTarget = EventTarget$1;
	}

	function _defineProperty(obj, key, value) {
	  if (key in obj) {
	    Object.defineProperty(obj, key, {
	      value: value,
	      enumerable: true,
	      configurable: true,
	      writable: true
	    });
	  } else {
	    obj[key] = value;
	  }

	  return obj;
	}

	class UIHandler extends EventTarget {
	  constructor(naja) {
	    super();

	    _defineProperty(this, "selector", '.ajax');

	    _defineProperty(this, "allowedOrigins", [window.location.origin]);

	    _defineProperty(this, "handler", this.handleUI.bind(this));

	    this.naja = naja;
	    naja.addEventListener('init', this.initialize.bind(this));
	  }

	  initialize() {
	    this.bindUI(window.document.body);
	    this.naja.snippetHandler.addEventListener('afterUpdate', event => {
	      const {
	        snippet
	      } = event.detail;
	      this.bindUI(snippet);
	    });
	  }

	  bindUI(element) {
	    const selectors = [`a${this.selector}`, `input[type="submit"]${this.selector}`, `input[type="image"]${this.selector}`, `button[type="submit"]${this.selector}`, `form${this.selector} input[type="submit"]`, `form${this.selector} input[type="image"]`, `form${this.selector} button[type="submit"]`].join(', ');

	    const bindElement = element => {
	      element.removeEventListener('click', this.handler);
	      element.addEventListener('click', this.handler);
	    };

	    const elements = element.querySelectorAll(selectors);

	    for (let i = 0; i < elements.length; i++) {
	      bindElement(elements.item(i));
	    }

	    if (element.matches(selectors)) {
	      bindElement(element);
	    }

	    const bindForm = form => {
	      form.removeEventListener('submit', this.handler);
	      form.addEventListener('submit', this.handler);
	    };

	    if (element.matches(`form${this.selector}`)) {
	      bindForm(element);
	    }

	    const forms = element.querySelectorAll(`form${this.selector}`);

	    for (let i = 0; i < forms.length; i++) {
	      bindForm(forms.item(i));
	    }
	  }

	  handleUI(event) {
	    if (event.altKey || event.ctrlKey || event.shiftKey || event.metaKey || event.button) {
	      return;
	    }

	    const element = event.currentTarget;
	    const options = {};

	    if (event.type === 'submit') {
	      this.submitForm(element, options, event);
	    } else if (event.type === 'click') {
	      this.clickElement(element, options, event);
	    }
	  }

	  clickElement(element, options = {}, event) {
	    let method, url, data;

	    if (!this.dispatchEvent(new CustomEvent('interaction', {
	      cancelable: true,
	      detail: {
	        element,
	        originalEvent: event,
	        options
	      }
	    }))) {
	      if (event) {
	        event.preventDefault();
	      }

	      return;
	    }

	    if (element.tagName === 'A') {
	      method = 'GET';
	      url = element.href;
	      data = null;
	    } else if (element.tagName === 'INPUT' || element.tagName === 'BUTTON') {
	      const {
	        form
	      } = element; // eslint-disable-next-line no-nested-ternary,no-extra-parens

	      method = element.hasAttribute('formmethod') ? element.getAttribute('formmethod').toUpperCase() : form.hasAttribute('method') ? form.getAttribute('method').toUpperCase() : 'GET';
	      url = element.getAttribute('formaction') || form.getAttribute('action') || window.location.pathname + window.location.search;
	      data = new FormData(form);

	      if (element.type === 'submit' || element.tagName === 'BUTTON') {
	        data.append(element.name, element.value || '');
	      } else if (element.type === 'image') {
	        const coords = element.getBoundingClientRect();
	        data.append(`${element.name}.x`, Math.max(0, Math.floor(event.pageX - coords.left)));
	        data.append(`${element.name}.y`, Math.max(0, Math.floor(event.pageY - coords.top)));
	      }
	    }

	    if (this.isUrlAllowed(url)) {
	      if (event) {
	        event.preventDefault();
	      }

	      this.naja.makeRequest(method, url, data, options);
	    }
	  }

	  submitForm(form, options = {}, event) {
	    if (!this.dispatchEvent(new CustomEvent('interaction', {
	      cancelable: true,
	      detail: {
	        element: form,
	        originalEvent: event,
	        options
	      }
	    }))) {
	      if (event) {
	        event.preventDefault();
	      }

	      return;
	    }

	    const method = form.hasAttribute('method') ? form.getAttribute('method').toUpperCase() : 'GET';
	    const url = form.getAttribute('action') || window.location.pathname + window.location.search;
	    const data = new FormData(form);

	    if (this.isUrlAllowed(url)) {
	      if (event) {
	        event.preventDefault();
	      }

	      this.naja.makeRequest(method, url, data, options);
	    }
	  }

	  isUrlAllowed(url) {
	    // ignore non-URL URIs (javascript:, data:, ...)
	    if (/^(?!https?)[^:/?#]+:/i.test(url)) {
	      return false;
	    }

	    return !/^https?/i.test(url) || this.allowedOrigins.some(origin => new RegExp(`^${origin}`, 'i').test(url));
	  }

	}

	class FormsHandler {
	  constructor(naja) {
	    _defineProperty(this, "netteForms", void 0);

	    this.naja = naja;
	    naja.addEventListener('init', this.initialize.bind(this));
	    naja.uiHandler.addEventListener('interaction', this.processForm.bind(this));
	  }

	  initialize() {
	    this.initForms(window.document.body);
	    this.naja.snippetHandler.addEventListener('afterUpdate', event => {
	      const {
	        snippet
	      } = event.detail;
	      this.initForms(snippet);
	    });
	  }

	  initForms(element) {
	    const netteForms = this.netteForms || window.Nette;

	    if (netteForms) {
	      if (element.tagName === 'form') {
	        netteForms.initForm(element);
	      }

	      const forms = element.querySelectorAll('form');

	      for (let i = 0; i < forms.length; i++) {
	        netteForms.initForm(forms.item(i));
	      }
	    }
	  }

	  processForm(event) {
	    const {
	      element,
	      originalEvent
	    } = event.detail;

	    if (element.form) {
	      element.form['nette-submittedBy'] = element;
	    }

	    const netteForms = this.netteForms || window.Nette;

	    if ((element.tagName === 'FORM' || element.form) && netteForms && !netteForms.validateForm(element)) {
	      if (originalEvent) {
	        originalEvent.stopImmediatePropagation();
	        originalEvent.preventDefault();
	      }

	      event.preventDefault();
	    }
	  }

	}

	class RedirectHandler extends EventTarget {
	  constructor(naja) {
	    super();
	    this.naja = naja;
	    naja.uiHandler.addEventListener('interaction', event => {
	      var _element$form;

	      const {
	        element,
	        options
	      } = event.detail;

	      if (!element) {
	        return;
	      }

	      if (element.hasAttribute('data-naja-force-redirect') || ((_element$form = element.form) === null || _element$form === void 0 ? void 0 : _element$form.hasAttribute('data-naja-force-redirect'))) {
	        var _element$getAttribute, _element$form2;

	        const value = (_element$getAttribute = element.getAttribute('data-naja-force-redirect')) !== null && _element$getAttribute !== void 0 ? _element$getAttribute : (_element$form2 = element.form) === null || _element$form2 === void 0 ? void 0 : _element$form2.getAttribute('data-naja-force-redirect');
	        options.forceRedirect = value !== 'off';
	      }
	    });
	    naja.addEventListener('success', event => {
	      const {
	        payload,
	        options
	      } = event.detail;

	      if (payload.redirect) {
	        this.makeRedirect(payload.redirect, options.forceRedirect, options);
	        event.stopImmediatePropagation();
	      }
	    });
	    this.locationAdapter = {
	      assign: url => window.location.assign(url)
	    };
	  }

	  makeRedirect(url, force, options = {}) {
	    if (url instanceof URL) {
	      url = url.href;
	    }

	    let isHardRedirect = force || !this.naja.uiHandler.isUrlAllowed(url);
	    const canRedirect = this.dispatchEvent(new CustomEvent('redirect', {
	      cancelable: true,
	      detail: {
	        url,
	        isHardRedirect,

	        setHardRedirect(value) {
	          isHardRedirect = !!value;
	        },

	        options
	      }
	    }));

	    if (!canRedirect) {
	      return;
	    }

	    if (isHardRedirect) {
	      this.locationAdapter.assign(url);
	    } else {
	      this.naja.makeRequest('GET', url, null, options);
	    }
	  }

	}

	class SnippetHandler extends EventTarget {
	  constructor(naja) {
	    super();

	    _defineProperty(this, "op", {
	      replace: (snippet, content) => {
	        snippet.innerHTML = content;
	      },
	      prepend: (snippet, content) => snippet.insertAdjacentHTML('afterbegin', content),
	      append: (snippet, content) => snippet.insertAdjacentHTML('beforeend', content)
	    });

	    naja.addEventListener('success', event => {
	      const {
	        options,
	        payload
	      } = event.detail;

	      if (payload.snippets) {
	        this.updateSnippets(payload.snippets, false, options);
	      }
	    });
	  }

	  updateSnippets(snippets, fromCache = false, options = {}) {
	    Object.keys(snippets).forEach(id => {
	      const snippet = document.getElementById(id);

	      if (snippet) {
	        this.updateSnippet(snippet, snippets[id], fromCache, options);
	      }
	    });
	  }

	  updateSnippet(snippet, content, fromCache, options) {
	    let operation = this.op.replace;

	    if ((snippet.hasAttribute('data-naja-snippet-prepend') || snippet.hasAttribute('data-ajax-prepend')) && !fromCache) {
	      operation = this.op.prepend;
	    } else if ((snippet.hasAttribute('data-naja-snippet-append') || snippet.hasAttribute('data-ajax-append')) && !fromCache) {
	      operation = this.op.append;
	    }

	    const canUpdate = this.dispatchEvent(new CustomEvent('beforeUpdate', {
	      cancelable: true,
	      detail: {
	        snippet,
	        content,
	        fromCache,
	        operation,

	        changeOperation(value) {
	          operation = value;
	        },

	        options
	      }
	    }));

	    if (!canUpdate) {
	      return;
	    }

	    if (snippet.tagName.toLowerCase() === 'title') {
	      document.title = content;
	    } else {
	      operation(snippet, content);
	    }

	    this.dispatchEvent(new CustomEvent('afterUpdate', {
	      cancelable: true,
	      detail: {
	        snippet,
	        content,
	        fromCache,
	        operation,
	        options
	      }
	    }));
	  }

	}

	class HistoryHandler {
	  constructor(naja) {
	    _defineProperty(this, "href", null);

	    _defineProperty(this, "uiCache", true);

	    this.naja = naja;
	    naja.addEventListener('init', this.initialize.bind(this));
	    naja.addEventListener('before', this.saveUrl.bind(this));
	    naja.addEventListener('success', this.pushNewState.bind(this));
	    naja.uiHandler.addEventListener('interaction', this.configureMode.bind(this));
	    this.popStateHandler = this.handlePopState.bind(this);
	    this.historyAdapter = {
	      replaceState: (data, title, url) => window.history.replaceState(data, title, url),
	      pushState: (data, title, url) => window.history.pushState(data, title, url)
	    };
	  }

	  initialize(event) {
	    const {
	      defaultOptions
	    } = event.detail;

	    if ('history' in defaultOptions && 'uiCache' in defaultOptions.history) {
	      this.uiCache = defaultOptions.history.uiCache;
	    }

	    window.addEventListener('popstate', this.popStateHandler);
	    this.historyAdapter.replaceState(this.buildState(window.location.href, this.uiCache), window.document.title, window.location.href);
	  }

	  handlePopState(e) {
	    if (!e.state) {
	      return;
	    }

	    if (e.state.ui) {
	      this.handleSnippets(e.state.ui);
	      this.handleTitle(e.state.title);
	    } else if (e.state.ui === false) {
	      this.naja.makeRequest('GET', e.state.href, null, {
	        history: false,
	        historyUiCache: false
	      });
	    }
	  }

	  saveUrl(event) {
	    const {
	      url
	    } = event.detail;
	    this.href = url;
	  }

	  configureMode(event) {
	    var _element$form, _element$form3;

	    const {
	      element,
	      options
	    } = event.detail; // propagate mode to options

	    if (!element) {
	      return;
	    }

	    if (element.hasAttribute('data-naja-history') || ((_element$form = element.form) === null || _element$form === void 0 ? void 0 : _element$form.hasAttribute('data-naja-history'))) {
	      var _element$getAttribute, _element$form2;

	      const value = (_element$getAttribute = element.getAttribute('data-naja-history')) !== null && _element$getAttribute !== void 0 ? _element$getAttribute : (_element$form2 = element.form) === null || _element$form2 === void 0 ? void 0 : _element$form2.getAttribute('data-naja-history');
	      options.history = this.constructor.normalizeMode(value);
	    }

	    if (element.hasAttribute('data-naja-history-cache') || ((_element$form3 = element.form) === null || _element$form3 === void 0 ? void 0 : _element$form3.hasAttribute('data-naja-history-nocache'))) {
	      var _element$getAttribute2, _element$form4;

	      const value = (_element$getAttribute2 = element.getAttribute('data-naja-history-cache')) !== null && _element$getAttribute2 !== void 0 ? _element$getAttribute2 : (_element$form4 = element.form) === null || _element$form4 === void 0 ? void 0 : _element$form4.getAttribute('data-naja-history-cache');
	      options.historyUiCache = value !== 'off';
	    }
	  }

	  static normalizeMode(mode) {
	    if (mode === 'off' || mode === false) {
	      return false;
	    } else if (mode === 'replace') {
	      return 'replace';
	    }

	    return true;
	  }

	  pushNewState(event) {
	    const {
	      payload,
	      options
	    } = event.detail;
	    const mode = this.constructor.normalizeMode(options.history);

	    if (mode === false) {
	      return;
	    }

	    if (payload.postGet && payload.url) {
	      this.href = payload.url;
	    }

	    const method = mode === 'replace' ? 'replaceState' : 'pushState';
	    const uiCache = options.historyUiCache === true || options.historyUiCache !== false && this.uiCache; // eslint-disable-line no-extra-parens

	    this.historyAdapter[method](this.buildState(this.href, uiCache), window.document.title, this.href);
	    this.href = null;
	  }

	  buildState(href, uiCache) {
	    const state = {
	      href
	    };

	    if (uiCache) {
	      state.title = window.document.title;
	      state.ui = this.findSnippets();
	    } else {
	      state.ui = false;
	    }

	    return state;
	  }

	  findSnippets() {
	    const result = {};
	    const snippets = window.document.querySelectorAll('[id^="snippet-"]');

	    for (let i = 0; i < snippets.length; i++) {
	      const snippet = snippets.item(i);

	      if (!snippet.hasAttribute('data-naja-history-nocache') && !snippet.hasAttribute('data-history-nocache')) {
	        result[snippet.id] = snippet.innerHTML;
	      }
	    }

	    return result;
	  }

	  handleSnippets(snippets) {
	    this.naja.snippetHandler.updateSnippets(snippets, true);
	    this.naja.scriptLoader.loadScripts(snippets);
	  }

	  handleTitle(title) {
	    window.document.title = title;
	  }

	}

	class ScriptLoader {
	  constructor(naja) {
	    naja.addEventListener('success', event => {
	      const {
	        payload
	      } = event.detail;

	      if (payload.snippets) {
	        this.loadScripts(payload.snippets);
	      }
	    });
	  }

	  loadScripts(snippets) {
	    Object.keys(snippets).forEach(id => {
	      const content = snippets[id];

	      if (!/<script/i.test(content)) {
	        return;
	      }

	      const el = window.document.createElement('div');
	      el.innerHTML = content;
	      const scripts = el.querySelectorAll('script');

	      for (let i = 0; i < scripts.length; i++) {
	        const script = scripts.item(i);
	        const scriptEl = window.document.createElement('script');
	        scriptEl.innerHTML = script.innerHTML;

	        if (script.hasAttributes()) {
	          const attrs = script.attributes;

	          for (let j = 0; j < attrs.length; j++) {
	            const attrName = attrs[j].name;
	            scriptEl[attrName] = attrs[j].value;
	          }
	        }

	        window.document.head.appendChild(scriptEl).parentNode.removeChild(scriptEl);
	      }
	    });
	  }

	}

	class Naja extends EventTarget {
	  constructor(uiHandler, redirectHandler, snippetHandler, formsHandler, historyHandler, scriptLoader) {
	    super();

	    _defineProperty(this, "VERSION", 2);

	    _defineProperty(this, "initialized", false);

	    _defineProperty(this, "uiHandler", null);

	    _defineProperty(this, "redirectHandler", null);

	    _defineProperty(this, "snippetHandler", null);

	    _defineProperty(this, "formsHandler", null);

	    _defineProperty(this, "historyHandler", null);

	    _defineProperty(this, "scriptLoader", null);

	    _defineProperty(this, "extensions", []);

	    _defineProperty(this, "defaultOptions", {});

	    this.uiHandler = uiHandler ? new uiHandler(this) : new UIHandler(this);
	    this.redirectHandler = redirectHandler ? new redirectHandler(this) : new RedirectHandler(this);
	    this.snippetHandler = snippetHandler ? new snippetHandler(this) : new SnippetHandler(this);
	    this.formsHandler = formsHandler ? new formsHandler(this) : new FormsHandler(this);
	    this.historyHandler = historyHandler ? new historyHandler(this) : new HistoryHandler(this);
	    this.scriptLoader = scriptLoader ? new scriptLoader(this) : new ScriptLoader(this);
	  }

	  registerExtension(extension) {
	    if (this.initialized) {
	      extension.initialize(this);
	    }

	    this.extensions.push(extension);
	  }

	  initialize(defaultOptions = {}) {
	    if (this.initialized) {
	      throw new Error('Cannot initialize Naja, it is already initialized.');
	    }

	    this.defaultOptions = defaultOptions;
	    this.extensions.forEach(extension => extension.initialize(this));
	    this.dispatchEvent(new CustomEvent('init', {
	      detail: {
	        defaultOptions
	      }
	    }));
	    this.initialized = true;
	  }

	  async makeRequest(method, url, data = null, options = {}) {
	    if (url instanceof URL) {
	      url = url.href;
	    }

	    options = { ...this.defaultOptions,
	      ...options,
	      fetch: { ...(this.defaultOptions.fetch || {}),
	        ...(options.fetch || {})
	      }
	    };

	    if (method.toUpperCase() === 'GET' && data instanceof FormData) {
	      const urlObject = new URL(url, location.href);

	      for (const [key, value] of data) {
	        urlObject.searchParams.append(key, value);
	      }

	      url = urlObject.toString();
	      data = null;
	    }

	    const abortController = new AbortController();
	    const request = new Request(url, {
	      credentials: 'same-origin',
	      ...options.fetch,
	      method,
	      headers: new Headers(options.fetch.headers || {}),
	      body: data !== null && Object.getPrototypeOf(data) === Object.prototype ? new URLSearchParams(data) : data,
	      signal: abortController.signal
	    }); // impersonate XHR so that Nette can detect isAjax()

	    request.headers.set('X-Requested-With', 'XMLHttpRequest');

	    if (!this.dispatchEvent(new CustomEvent('before', {
	      cancelable: true,
	      detail: {
	        request,
	        method,
	        url,
	        data,
	        options
	      }
	    }))) {
	      return {};
	    }

	    const promise = window.fetch(request);
	    this.dispatchEvent(new CustomEvent('start', {
	      detail: {
	        request,
	        promise,
	        abortController,
	        options
	      }
	    }));
	    let response, payload;

	    try {
	      response = await promise;

	      if (!response.ok) {
	        throw new HttpError(response);
	      }

	      payload = await response.json();
	    } catch (error) {
	      if (error.name === 'AbortError') {
	        this.dispatchEvent(new CustomEvent('abort', {
	          detail: {
	            request,
	            error,
	            options
	          }
	        }));
	        this.dispatchEvent(new CustomEvent('complete', {
	          detail: {
	            request,
	            response,
	            payload: undefined,
	            error,
	            options
	          }
	        }));
	        return {};
	      }

	      this.dispatchEvent(new CustomEvent('error', {
	        detail: {
	          request,
	          response,
	          error,
	          options
	        }
	      }));
	      this.dispatchEvent(new CustomEvent('complete', {
	        detail: {
	          request,
	          response,
	          payload: undefined,
	          error,
	          options
	        }
	      }));
	      throw error;
	    }

	    this.dispatchEvent(new CustomEvent('success', {
	      detail: {
	        request,
	        response,
	        payload,
	        options
	      }
	    }));
	    this.dispatchEvent(new CustomEvent('complete', {
	      detail: {
	        request,
	        response,
	        payload,
	        error: undefined,
	        options
	      }
	    }));
	    return payload;
	  }

	}
	class HttpError extends Error {
	  constructor(response) {
	    const message = `HTTP ${response.status}: ${response.statusText}`;
	    super(message);
	    this.name = this.constructor.name;
	    this.stack = new Error(message).stack;
	    this.response = response;
	  }

	}

	class AbortExtension {
	  constructor() {
	    _defineProperty(this, "abortable", true);

	    _defineProperty(this, "abortController", null);
	  }

	  initialize(naja) {
	    naja.uiHandler.addEventListener('interaction', this.checkAbortable.bind(this));
	    naja.addEventListener('init', this.onInitialize.bind(this));
	    naja.addEventListener('before', this.checkAbortable.bind(this));
	    naja.addEventListener('start', this.saveAbortController.bind(this));
	    naja.addEventListener('complete', this.clearAbortController.bind(this));
	  }

	  onInitialize() {
	    document.addEventListener('keydown', event => {
	      if (this.abortController !== null && ('key' in event ? event.key === 'Escape' : event.keyCode === 27) && !(event.ctrlKey || event.shiftKey || event.altKey || event.metaKey) && this.abortable) {
	        this.abortController.abort();
	        this.abortController = null;
	      }
	    });
	  }

	  checkAbortable(event) {
	    var _element$getAttribute, _element$form;

	    const {
	      element,
	      options
	    } = event.detail;
	    this.abortable = element ? ((_element$getAttribute = element.getAttribute('data-naja-abort')) !== null && _element$getAttribute !== void 0 ? _element$getAttribute : (_element$form = element.form) === null || _element$form === void 0 ? void 0 : _element$form.getAttribute('data-naja-abort')) !== 'off' // eslint-disable-line no-extra-parens
	    : options.abort !== false; // propagate to options if called in interaction event

	    options.abort = this.abortable;
	  }

	  saveAbortController(event) {
	    const {
	      abortController
	    } = event.detail;
	    this.abortController = abortController;
	  }

	  clearAbortController() {
	    this.abortController = null;
	    this.abortable = true;
	  }

	}

	class UniqueExtension {
	  constructor() {
	    _defineProperty(this, "abortControllers", new Map());
	  }

	  initialize(naja) {
	    naja.uiHandler.addEventListener('interaction', this.checkUniqueness.bind(this));
	    naja.addEventListener('start', this.abortPreviousRequest.bind(this));
	    naja.addEventListener('complete', this.clearRequest.bind(this));
	  }

	  checkUniqueness(event) {
	    var _element$getAttribute, _element$form;

	    const {
	      element,
	      options
	    } = event.detail;
	    const unique = (_element$getAttribute = element.getAttribute('data-naja-unique')) !== null && _element$getAttribute !== void 0 ? _element$getAttribute : (_element$form = element.form) === null || _element$form === void 0 ? void 0 : _element$form.getAttribute('data-naja-unique');
	    options.unique = unique === 'off' ? false : unique !== null && unique !== void 0 ? unique : 'default';
	  }

	  abortPreviousRequest(event) {
	    const {
	      abortController,
	      options
	    } = event.detail;

	    if (options.unique !== false) {
	      var _this$abortController;

	      (_this$abortController = this.abortControllers.get(options.unique)) === null || _this$abortController === void 0 ? void 0 : _this$abortController.abort();
	      this.abortControllers.set(options.unique, abortController);
	    }
	  }

	  clearRequest(event) {
	    const {
	      request,
	      options
	    } = event.detail;

	    if (!request.signal.aborted) {
	      this.abortControllers.delete(options.unique);
	    }
	  }

	}

	const naja = new Naja();
	naja.registerExtension(new AbortExtension());
	naja.registerExtension(new UniqueExtension());
	naja.Naja = Naja;
	naja.HttpError = HttpError;

	return naja;

})));
//# sourceMappingURL=Naja.js.map
