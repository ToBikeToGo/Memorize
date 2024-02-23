import React, { useEffect, useState } from 'react';

import styled from'styled-components';
import { API_URL } from '../helpers/getApiUrl';


const StyledCard = styled.div`

background-color: #fff;
padding: 3em;
border-radius: 3em;
margin: 3em;
padding: 5em;
display: flex;
flex-direction: column;
justify-content: center;
align-items: center;
width: 100%;


h3, p {
    color: #000;
}

`


const Card = ({ card }) => (
  <StyledCard>
    <span
    className='text-lg text-black p-3 bg-slate-200 rounded-xl ml-auto mb-5'
    >
      {card.tag}
    </span>
    <h3 className='text-3xl mb-4'> {card.question} </h3> <p> {card.answer} </p>
  </StyledCard>
);

const CardsPage = () => {
  const [cards, setCards] = useState([]);
  const [tags, setTags] = useState('');

  useEffect(() => {
    const fetchCards = async () => {
      try {
        const response = await fetch(`${API_URL}/cards?tag=${tags}`);
        const data = await response.json();
        setCards(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des cartes', error);
      }
    };

    fetchCards();
  }, [tags]); 

  return (
    <div>
      <h2> Cartes </h2>{' '}
      <input
          data-testid="tags-input"
        type="text"
        value={tags}
        onChange={(e) => setTags(e.target.value)}
        placeholder="Entrez des tags séparés par des virgules"
      />
      <div>
        {' '}
        {cards.length > 0 ? (
          cards.map((card) => <Card key={card.id} card={card} />)
        ) : (
          <p> Aucune carte trouvée </p>
        )}{' '}
      </div>{' '}
    </div>
  );
};

export default CardsPage;
