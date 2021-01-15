import {h, render} from 'preact';
import {Router, Link} from 'preact-router';

import Home       from './pages/home';
import Conference from './pages/conference';

function App() {
  return (
    <div>
      <header>
        <Link href="/">Home</Link>
        <br />
        <Link href="/conferences/amsterdam-2019">Amsterdam 2019</Link>
      </header>
      <Router>
        <Home path="/" />
        <Conference path="/conferences/:slug" />
      </Router>
    </div>
  )
}

render(<App />, document.getElementById('app'));
