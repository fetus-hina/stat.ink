import Blog from './parts/Blog';
import LatestBattles from './parts/LatestBattles';
import MyLatestBattles from './parts/MyLatestBattles';
// import Schedule from './parts/Schedule';

interface AppProps {
  loggedIn: boolean;
}

export default function App ({ loggedIn }: AppProps) {
  return (
    <>
      <Blog />
      {/* <Schedule /> */}
      {loggedIn ? <MyLatestBattles /> : null}
      <LatestBattles />
    </>
  );
}
