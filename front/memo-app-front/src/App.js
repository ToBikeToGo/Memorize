import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { ShowQuizz } from './pages/ShowQuizz';
import CreateFlashCardPage from './pages/CreateFlashCardPage'; // Supposons que vous avez ce composant
import CardsPage from './pages/CardsPage';

function App() {

  return (
    <Router>
      <div className="app flex flex-col w-full items-center  h-full bg-slate-100" style={{
        minHeight: '100vh',
      }}>

      <div className={'p-10 bg-sky-700 w-full flex'}>
        <nav>
          <ul>
            <li>
              <Link className={"text-white"} to="/show-quizz">Show Quizz</Link>
            </li>
            <li>
              <Link className={"text-white"} to="/create-flash-card">Create Flash Card</Link>
            </li>
            <li>
              <Link className={"text-white"} to="/cards">Cards</Link>
              </li>
          </ul>
        </nav>
        <h2 className={"text-4xl font-bold text-white m-auto"}>Memorize</h2>
      </div>

        <Routes>
          <Route path="/show-quizz" element={<ShowQuizz />} />
          <Route path="/create-flash-card" element={<CreateFlashCardPage />} />
          <Route path="/cards" element={<CardsPage />} />
          <Route path="/" element={<ShowQuizz />} />
        </Routes>
      </div>

    </Router>
  );
}

const Home = () => (
  <div>
    <h2 className={"text-xxl font-bold"}>Memorize</h2>
  </div>
);

export default App;
