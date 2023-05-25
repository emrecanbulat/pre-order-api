<?php

namespace App\Console\Commands;

use App\Libraries\MessageHelper;
use App\Models\Order;
use Illuminate\Console\Command;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class OrderStatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-status:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        logger()->info('Cron is working fine!');

        $orders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', now()->subDays(1))
            ->get();

        foreach ($orders as $order) {
            $order->status = Order::STATUS_REJECTED;
            $order->save();
            logger()->info('Order status changed to rejected. Order ID: ' . $order->id);

            try {
                logger()->info('Sending message to user: ' . $order->user->phone);
                MessageHelper::sendMessage(MessageHelper::REJECTED_MESSAGE, "+905551808618");//$order->user->phone);
            } catch (ConfigurationException $e) {
                logger()->info('Twilio configuration error: ' . $e->getMessage());
            } catch (TwilioException $e) {
                logger()->info('Twilio error: ' . $e->getMessage());
            } catch (\Exception $e) {
                logger()->info('Error: ' . $e->getMessage());
            }
        }
    }
}
