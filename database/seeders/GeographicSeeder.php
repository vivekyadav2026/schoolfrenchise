<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\State;
use Illuminate\Database\Seeder;

class GeographicSeeder extends Seeder
{
    public function run(): void
    {
        $statesWithDistricts = [
            'Uttar Pradesh' => ['Noida (Gautam Buddha Nagar)', 'Lucknow', 'Kanpur', 'Varanasi', 'Prayagraj', 'Agra', 'Ghaziabad', 'Gorakhpur', 'Meerut', 'Aligarh', 'Bareilly', 'Moradabad'],
            'Bihar' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Purnia', 'Darbhanga', 'Begusarai', 'Motihari', 'Ara', 'Samastipur', 'Katihar', 'Chapra'],
            'Delhi' => ['New Delhi', 'Central Delhi', 'East Delhi', 'North Delhi', 'South Delhi', 'West Delhi', 'North East Delhi', 'South West Delhi'],
            'Jharkhand' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Deoghar', 'Hazaribagh', 'Giridih'],
            'Madhya Pradesh' => ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Sagar', 'Rewa', 'Satna'],
            'Rajasthan' => ['Jaipur', 'Jodhpur', 'Kota', 'Bikaner', 'Ajmer', 'Udaipur', 'Bhilwara', 'Alwar'],
            'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Bardhaman', 'Malda'],
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati'],
            'Haryana' => ['Gurugram', 'Faridabad', 'Panipat', 'Ambala', 'Karnal', 'Hisar', 'Rohtak'],
            'Punjab' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali'],
            'Uttarakhand' => ['Dehradun', 'Haridwar', 'Roorkee', 'Haldwani', 'Nainital', 'Rishikesh'],
            'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Gandhinagar'],
            'Chhattisgarh' => ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Durg', 'Rajnandgaon'],
            'Odisha' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri'],
            'Assam' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia'],
            'Andhra Pradesh' => ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Tirupati'],
            'Telangana' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Khammam'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli'],
            'Karnataka' => ['Bengaluru', 'Mysuru', 'Hubballi', 'Mangaluru', 'Belagavi', 'Kalaburagi'],
            'Kerala' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam', 'Kannur'],
            'Himachal Pradesh' => ['Shimla', 'Dharamshala', 'Mandi', 'Solan', 'Kullu', 'Hamirpur'],
            'Jammu & Kashmir' => ['Srinagar', 'Jammu', 'Anantnag', 'Baramulla', 'Udhampur', 'Kathua'],
            'Chandigarh' => ['Chandigarh'],
            'Goa' => ['North Goa', 'South Goa'],
            'Tripura' => ['Agartala', 'Udaipur', 'Dharmanagar'],
            'Manipur' => ['Imphal West', 'Imphal East', 'Thoubal'],
            'Meghalaya' => ['East Khasi Hills (Shillong)', 'West Garo Hills'],
            'Nagaland' => ['Kohima', 'Dimapur'],
            'Mizoram' => ['Aizawl', 'Lunglei'],
            'Sikkim' => ['East Sikkim (Gangtok)', 'West Sikkim'],
            'Arunachal Pradesh' => ['Papum Pare (Itanagar)', 'Changlang'],
            'Puducherry' => ['Puducherry', 'Karaikal'],
            'Andaman & Nicobar Islands' => ['South Andaman (Port Blair)', 'North & Middle Andaman'],
            'Dadra & Nagar Haveli and Daman & Diu' => ['Daman', 'Diu', 'Silvassa'],
            'Ladakh' => ['Leh', 'Kargil'],
            'Lakshadweep' => ['Kavaratti'],
        ];

        foreach ($statesWithDistricts as $stateName => $districts) {
            $state = State::firstOrCreate(['name' => $stateName]);
            foreach ($districts as $districtName) {
                District::firstOrCreate([
                    'state_id' => $state->id,
                    'name' => $districtName,
                ]);
            }
        }
    }
}
