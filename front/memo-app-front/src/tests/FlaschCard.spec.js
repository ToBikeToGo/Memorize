import '@testing-library/jest-dom';

import {
    fireEvent,
    render,
    screen,
} from '@testing-library/react';

import {FlashCard} from '../components/FlashCard';

const mockCards = [{
    "id": 6384,
    "question": "question0",
    "answer": "answer0",
    "category": "THIRD",
    "tag": "tag0"
},
    {
        "id": 6385,
        "question": "question1",
        "answer": "answer1",
        "category": "SECOND",
        "tag": "tag1"
    },
    {
        "id": 6386,
        "question": "question2",
        "answer": "answer2",
        "category": "SECOND",
        "tag": "tag2"
    },
    {
        "id": 6387,
        "question": "question3",
        "answer": "answer3",
        "category": "FIRST",
        "tag": "tag3"
    },
    {
        "id": 6388,
        "question": "question4",
        "answer": "answer4",
        "category": "FIRST",
        "tag": "tag4"
    },
]


test('loads and display question', () => {

    const mockedCardsWithFirstArray = mockCards.slice(0, 1);

    render(<FlashCard cards={mockedCardsWithFirstArray}/>);

    const question = screen.getByText(/question0/i);
    expect(question).toBeInTheDocument()

});


test('loads and display answer', async () => {
    const mockedCardsWithFirstArray = mockCards.slice(0, 1);
    render(<FlashCard cards={mockedCardsWithFirstArray}/>);
    const knowAnswerButton = screen.getByTestId('know-answer');
    knowAnswerButton.click();

    const answer = await screen.findByText(/answer0/i);
    expect(answer).toBeInTheDocument()
});


test('loads and display next question', async () => {
    render(<FlashCard cards={mockCards}/>);
    const knowAnswerButton = screen.getByTestId('know-answer');
    knowAnswerButton.click();

    const answer = await screen.findByText(/answer0/i);
    expect(answer).toBeInTheDocument()

    const nextButton = screen.getByTestId('next');
    nextButton.click();

    const question = await screen.findByText(/question1/i);
    expect(question).toBeInTheDocument()
});


test('renders and clicks "Je ne connais pas la réponse" button', () => {
  render(<FlashCard cards={mockCards} />);
  const button = screen.getByTestId('know-answer');
  expect(button).toBeInTheDocument();
  fireEvent.click(button);
});

test('renders and clicks "I know answer" button', () => {
  render(<FlashCard cards={mockCards} />);
  const button = screen.getByRole('button', { name: /Je connais la réponse/i });
  expect(button).toBeInTheDocument();
  fireEvent.click(button);
});

test('renders and clicks "Next" button', async () => {
  render(<FlashCard cards={mockCards} />);
  const knowAnswerButton = screen.getByTestId('know-answer');
  fireEvent.click(knowAnswerButton);
  const nextButton = await screen.findByTestId('next');
  expect(nextButton).toBeInTheDocument();
  fireEvent.click(nextButton);
});

test('renders and clicks "I know answer finally" button', async () => {
  render(<FlashCard cards={mockCards} />);
  const knowAnswerButton = screen.getByTestId('know-answer');
  fireEvent.click(knowAnswerButton);
  const forceValidationButton = await screen.findByTestId('force-validation');
  expect(forceValidationButton).toBeInTheDocument();
  fireEvent.click(forceValidationButton);
});








