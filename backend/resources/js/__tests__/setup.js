class IntersectionObserverMock {
    constructor(callback) {
        this.callback = callback;
    }

    observe() {
        this.callback([{ isIntersecting: true }]);
    }

    disconnect() {}
    unobserve() {}
}

global.IntersectionObserver = IntersectionObserverMock;
