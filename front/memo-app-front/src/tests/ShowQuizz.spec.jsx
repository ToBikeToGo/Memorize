import '@testing-library/jest-dom';

import {
  render,
  screen,
} from '@testing-library/react';

import { ShowQuizz } from '../pages/ShowQuizz';

test('render Show quizz', () => {
    render( <ShowQuizz /> );
    const linkElement = screen.getByText(/Chargement des cartes ou aucune carte disponible./i);
    expect(linkElement).toBeInTheDocument();
});

